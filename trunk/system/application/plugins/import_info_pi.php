<?php

class ImportInfo
{
  private $ordered_sequences = array();
  private $labels = array();
  private $count = 0;
  private $label_model = null;
  private $sequence_model = null;
  private $label_sequence_model = null;
  private $taxonomy_model = null;
  
  private $event_model = null;
  private $event_data = null;
  private $event_component = null;
  private $event_put = null;
  
  function ImportInfo(&$event_data = null, $event_component = null)
  {
    $this->label_model = load_ci_model('label_model');
    $this->sequence_model = load_ci_model('sequence_model');
    $this->label_sequence_model = load_ci_model('label_sequence_model');
    $this->taxonomy_model = load_ci_model('taxonomy_model');
    
    if($event_data && $event_component) {
      $this->event_component = $event_component;
      $this->event_data =& $event_data;
      $this->event_data[$this->event_component] = array('base_sequences' => 0, 'total_labels' => 0);
      $this->event_put =& $this->event_data[$this->event_component];
      $this->event_model = load_ci_model('event_model');
    }
  }
  
  private function __has_event()
  {
    return $this->event_data && $this->event_component;
  }
  
  private function __update_event()
  {
    if($this->__has_event())
    {
      $data = json_encode($this->event_data);
      $this->event_model->set($this->event_data['event'], $data);
    }
  }
  
  public function duo_match($info2)
  {
    $total1 = count($this->ordered_sequences);
    $total2 = count($info2->ordered_sequences);
    
    return $total1 == $total2;
  }
  
  public function all_type($wanted_type)
  {
    foreach($this->ordered_sequences as &$seq) {
      if($seq['type'] != $wanted_type)
        return false;
    }
    
    return true;
  }
  
  public function all_dna()
  {
    return $this->all_type('dna');
  }
  
  public function all_protein()
  {
    return $this->all_type('protein');
  }
  
  public function link_sequences($info2)
  {
    $i = 0;
    foreach($this->ordered_sequences as &$seq) {
      $dna_id = $seq['id'];
      $seq2 =& $info2->ordered_sequences[$i++];
      $protein_id = $seq2['id'];
      
      $this->sequence_model->set_translated_sequence($dna_id, $protein_id);
    }
  }
  
  // writes imported sequences to a temporary fasta file returning the temporary filename
  private function write_simple_fasta()
  {
    $temp_file = generate_new_file_name();
    $fp = fopen($temp_file, 'w');

    foreach($this->ordered_sequences as &$seq) {
      $name = $seq['name'];
      $id = $seq['id'];
      $content = $this->sequence_model->get_content($id);
      
      fwrite($fp, ">$name\n$content\n");
    }
    fclose($fp);
    
    return $temp_file;
  }
  
  public function convert_protein_file()
  { 
    $CI =& get_instance();
    $CI->load->library('SequenceConverter');
   
    $fasta = $this->write_simple_fasta();
    
    $protein = $CI->sequenceconverter->convert_dna_fasta($fasta);
    
    unlink($fasta);
    
    return $protein;
  }
  
  public function add_label($name)
  {
    $this->labels[$name] = array();
  }
  
  public function get_labels()
  {
    $ret = array();
    
    foreach($this->labels as $name => $data) {
      $ret[] = $name;
    }
    
    return $ret;
  }
  
  public function add_sequence($name, $content, $id = null)
  {
    $content = sequence_normalize($content);
    $type = sequence_type($content);
    $name = $this->__fix_sequence_name(trim($name), $type);
    
    $data = array('name' => $name, 'type' => $type, 'content' => $content, 'id' => $id, 'labels' => array());
    
    $this->__import_base_sequence($data);
    
    ++$this->count;
    
    if($this->__has_event()) {
      ++$this->event_put['base_sequences'];
      $this->__update_event();
    }
    
    //error_log("Imported sequence with name: $name ".$this->count, 0);
    
    $this->ordered_sequences[] = $data;
  }
  
  public function add_sequence_label($sequence, $label, $value, $param = null)
  {
    $sequence = trim($sequence);
    $label = trim($label);
    
    if(!array_key_exists($label, $this->labels)) {
      return false;
    }
    
    // find sequence
    for($i = count($this->ordered_sequences) - 1; $i >= 0; --$i) {
      $sequence_data =& $this->ordered_sequences[$i];
      
      if($sequence_data['name'] == $sequence) {
        $labels =& $sequence_data['labels'];
        
        $label_data = new LabelData($value, $param);
        
        if(array_key_exists($label, $labels)) {
          $current_label_info =& $labels[$label];
          $values =& $current_label_info['values'];
          $values[] = $label_data;
        } else {
          $labels[$label] = array('values' => array($label_data));
        }
        
        return true;
      }
    }
    
    return false;
  }
  
  private function __get_labels()
  {
    $new_labels = array();
    
    foreach($this->labels as $name => $d) {
      $data = array();
      
      if($name == 'name') {
        $data['status'] = 'base';
        $data['type'] = 'text';
      } else {
        if(!$this->label_model->has($name))
          $data['status'] = 'not_found';
        else {
          $new_label = $this->label_model->get_by_name($name);
        
          if($new_label) {
            $data['status'] = 'ok';
            $data['data'] = $new_label;
            $data['type'] = $new_label['type'];
          } else {
            $data['status'] = 'not_found';
          }
        }
      }
    
      $new_labels[$name] = $data;
    }
    
    $this->labels = $new_labels;
  }
  
  private function __import_label_text_natural($value, $type)
  {
    $data = label_get_data($value);
    $param = label_get_param($value);
    
    switch($type) {
    case 'bool':
      return $data == '0' ? 'No' : 'Yes';
    }
    
    if($param) {
      return "$param -> $data";
    } else {
      return $data;
    }
  }
  
  private function __get_ref_value($seq_id, $name, $value, &$seq_labels)
  {
    $value = label_get_data($value);
    
    if($name == 'translated') {
      $type = $this->sequence_model->get_type($seq_id);
      
      if(!valid_sequence_type($type)) {
        return null;
      }
      
      $other_type = ($type == 'dna' ? 'protein' : 'dna');
      
      return $this->sequence_model->locate_sequence_type($value, $other_type);
    } else if($name == 'super') {
      $all = $this->sequence_model->locate_all($value);
      
      if(count($all) == 0)
        return null;
      
      $start = null;
      $length = null;
      
      // check if there is some label super_position in the file 
      // for this sequence
      $label_position =& $seq_labels['super_position'];
      
      if($label_position) {
        $values = $label_position['values'];
        
        if(count($values) > 0) {
          $value = $values[0];
          
          $data = label_get_data($value);
          
          $vec = explode(' ', $data);
          if(count($vec) == 2 && isint($vec[0]) && isint($vec[1])) {
            $start = (int)$vec[0];
            $length = (int)$vec[1];
          }
        }
      }
      
      if(!$start) {
        $position = $this->sequence_model->get_super_position($seq_id);
        
        if($position) {
          $start = $position[0];
          $length = $position[1];
        }
      }
      
      if(!$start)
        return null;
      
      $cmp_content = $this->sequence_model->get_content($seq_id);
      
      foreach($all as $other_id) {
        $content = $this->sequence_model->get_content_segment($other_id, $start, $length);
        
        if($content == $cmp_content) {
          // add subsequence label to super sequence
          $subsequence_id = $this->label_model->get_id_by_name('subsequence');
          if($subsequence_id) {
            $this->label_sequence_model->add_ref_label($other_id, $subsequence_id,
              new LabelData($seq_id, "$start,$length"));
          }
          return $other_id;
        }
      }
      
      return null;
    } else {
      $all = $this->sequence_model->locate_all($value);
      
      if(count($all) > 0) {
        return $all[0];
      }
      
      return null;
    }
    
    return $value;
  }
  
  private function __get_tax_value($value)
  {
    $value = label_get_data($value);
    $row = $this->taxonomy_model->get_by_name($value);
    return $row['id'];
  }
  
  private function __update_label_content($id, $seq_id, $type, $name, $value, &$seq_labels)
  {
    if(label_get_data($value) == '') {
      return;
    }
    
    $model = $this->label_sequence_model;
    
    switch($type) {
      case 'integer':
        return $model->edit_integer_label($id, $value);
      case 'float':
        return $model->edit_float_label($id, $value);
      case 'text':
        return $model->edit_text_label($id, $value);
      case 'position':
        $data = label_get_data($value);
        $vec = explode(' ', $data);
        if(count($vec) != 2) {
          return false;
        }
        return $model->edit_position_label($id, $vec[0], $vec[1], label_get_param($value));
      case 'url':
        return $model->edit_url_label($id, $value);
      case 'bool':
        return $model->edit_bool_label($id, $value);
      case 'date':
        return $model->edit_date_label($id, $value);
      case 'ref':
        $data = $this->__get_ref_value($seq_id, $name, $value, $seq_labels);
        if($data) {
          return $model->edit_ref_label($id, $data);
        } else {
          return null;
        }
      case 'tax':
        return $model->edit_tax_label($id, $this->__get_tax_value($value));
    }
    
    return null;
  }
  
  private function __add_label_content($seq_id, $label_id, $label_name, $label_type, $value, &$seq_labels)
  {
    if(label_get_data($value) == '') {
      return;
    }
    
    $model = $this->label_sequence_model;

    switch($label_type) {
      case 'integer':
        return $model->add_integer_label($seq_id, $label_id, $value);
      case 'float':
        return $model->add_float_label($seq_id, $label_id, $value);
      case 'text':
        return $model->add_text_label($seq_id, $label_id, $value);
      case 'position':
        $data = label_get_data($value);
        $vec = explode(' ', $data);
        if(count($vec) != 2) {
          return false;
        }
        return $model->add_position_label($seq_id, $label_id, $vec[0], $vec[1], label_get_param($value));
      case 'ref':
        $data = $this->__get_ref_value($seq_id, $label_name, $value, $seq_labels);
        if($data)
          return $model->add_ref_label($seq_id, $label_id, $data);
        else
          return null;
      case 'tax':
        $id = $this->__get_tax_value($value);
        if($id)
          return $model->add_tax_label($seq_id, $label_id, $id);
        else
          return null;
      case 'url':
        return $model->add_url_label($seq_id, $label_id, $value);
      case 'bool':
        return $model->add_bool_label($seq_id, $label_id, $value);
      case 'date':
        return $model->add_text_label($seq_id, $label_id, $value);
    }
    
    return null;
  }
  
  private function __import_sequence_label(&$seq_data, $label_name, &$label_data)
  {
    $seq_labels =& $seq_data['labels'];
    $this_label =& $seq_labels[$label_name];
    $label_info =& $label_data['data'];
    $label_type = $label_info['type'];
    
    if($label_name == 'name')
      return;
    
    if($this_label == null) {
      $this_label['status'] = 'Empty / Not inserted / Unchanged';
      return;
    }
    
    if($label_data['status'] == 'not_found') {
      $this_label['status'] = 'Label is not installed';
      return;
    }
    
    $seq_id = $seq_data['id'];
    $label_id = $label_info['id'];
    
    $already_there = $this->label_sequence_model->label_used_up($seq_id, $label_id);
    
    $editable = $label_info['editable'];
    $multiple = $label_info['multiple'];
    $values =& $this_label['values'];
    
    if($already_there && !$editable && !$multiple) {
      $this_label['status'] = 'Already inserted';
    } else if($already_there) {
      $id = $this->label_sequence_model->get_label_id($seq_id, $label_id);
      
      $this_label['status'] = $this->__update_inserted_label($values, $label_info, $id, $seq_id, $seq_labels);
      
    } else if(!$already_there)
      $this_label['status'] = $this->__insert_label($values, $label_info, $seq_id, $seq_labels);
    
    unset($this_label['values']);
  }
  
  private function __insert_label($values, $label_info, $seq_id, &$seq_labels)
  {
    if($label_info['auto_on_creation']) {
      $this->label_sequence_model->add_auto_label($seq_id, $label_info);
      return 'Generated';
    }
    
    $label_name = $label_info['name'];
    $label_type = $label_info['type'];
    $label_id = $label_info['id'];
    
    if($label_info['multiple']) {
      // this is a multiple value label
      
      if($this->__empty_values($values)) {
        if($label_info['editable'])
          return 'Empty value';
        else if($label_info['code']) {
          $this->label_sequence_model->add_auto_label($seq_id, $label_info);
          return 'Generated';
        } else
          return 'Empty value';
      }
      
      $status_text = 'Values:';
        
      foreach($values as $value) {
          
        if($value == '') {
          $status_text .= ' (Empty, ERROR)';
          continue;
        }
          
        $text_natural = $this->__import_label_text_natural($value, $label_type);
          
        if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value, $seq_labels))
          $status_text .= " ($text_natural, OK)";
        else
          $status_text .= " ($text_natural, ERROR)";
      }
        
      return $status_text;
    }
    
    $value = $values[0];
    
    if(label_get_data($value) == '') {
      if($label_info['editable'])
        return 'Empty value';
      else if($label_info['code']) {
        $this->label_sequence_model->add_auto_label($seq_id, $label_info);
        return 'Generated';
      } else
        return 'Empty value';
    }
    
    return $this->__add_single_label($seq_id, $label_id, $label_name, $label_type, $value, $seq_labels);
  }
  
  private function __empty_values($values)
  {
    return count($values) == 0 || (count($values) == 1 && label_get_data($values[0]) == '');
  }
  
  private function __update_inserted_label($values, $label_info, $label_seq_id, $seq_id, &$seq_labels)
  {
    $label_type = $label_info['type'];
    $label_name = $label_info['name'];
    $label_id = $label_info['id'];
    $multiple = $label_info['multiple'];
    
    if($multiple) {
      if($this->__empty_values($values)) {
        // auto generate
        
        if($label_info['editable'])
          return 'Empty value';
        elseif($label_info['code']) {
          $this->label_sequence_model->regenerate_label($label_id, $seq_id);
          return 'Regenerated';
        } else
          return 'Empty value';
        
      } else {
        // multiple editing: just add
        $ret = 'Values: ';
        
        foreach($values as $value) {
          $text_natural = $this->__import_label_text_natural($value, $label_type);
          
          if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value, $seq_labels)) {
            $ret .= " ($text_natural, OK)";
          } else {
            $ret .= " ($text_natural, ERROR)";
          }
        }
        
        return $ret;
      }
    } else {
      $value = $values[0];
      
      if(label_get_data($value) == '') {
        if($label_info['editable'])
          return 'Empty value';
        elseif($label_info['code']) {
          $this->label_sequence_model->edit_auto_label($label_seq_id);
          return 'Regenerated';
        } else
          return 'Empty value';
      } else {
        $text_natural = $this->__import_label_text_natural($value, $label_type);
    
        if($this->__update_label_content($label_seq_id, $seq_id, $label_type, $label_name, $value, $seq_labels)) {
          return "Updated value: $text_natural";
        } else {
          return "Parse error: $text_natural";
        }
      }
    }
  }
  
  private function __add_single_label($seq_id, $label_id, $label_name, $label_type, $value, &$seq_labels)
  {
    if($value == '') {
      return 'Empty / Not inserted';
    }
    
    $text_natural = $this->__import_label_text_natural($value, $label_type);
    if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value, $seq_labels))
      return "Value: $text_natural";
    else
      return "Parse error: $text_natural";
  }
  
  private function __import_labels(&$data)
  {
    $seq_name = $data['name'];
    
    foreach($this->labels as $name => &$label_data) {
      $this->__import_sequence_label($data, $name, $label_data);
    }
  }
  
  private function __is_generated_protein_name($name)
  {
    return preg_match('/_[0-9]$/', $name);
  }
  
  private function __fix_protein_name($name)
  {
    return preg_replace('/_[0-9]$/', '_p', $name);
  }
  
  private function __fix_sequence_name($old_name, $type)
  {
    if($type == 'protein' && $this->__is_generated_protein_name($old_name))
    {
      return $this->__fix_protein_name($old_name);
    }
    
    return $old_name;
  }
  
  private function __import_base_sequence(&$data)
  {
    $content =& $data['content'];
    $isnew = false;
    $name = $data['name'];
    
    if($this->sequence_model->has_same_sequence($name, $content)) {
      $data['id'] = $this->sequence_model->get_id_by_name_and_content($name, $content);
      $data['short_content'] = $this->sequence_model->get_short_content($data['id']);
      $data['comment'] = 'Sequence name and content are identical.';
    } else {
      $isnew = true;
      $data['comment'] = '';
      $data['id'] = $this->sequence_model->add($name, $content);
      $data['short_content'] = sequence_short_content($data['content']);
      unset($data['content']);
    }
    
    $data['add'] = $isnew;
  }
  
  public function import()
  {
    $this->__get_labels();
    
    //$count = 0;
    //$total = count($this->ordered_sequences);
    
    foreach($this->ordered_sequences as &$data) {
      $name = $data['name'];
      //++$count;
      //error_log("Importing sequence labels with name: $name $count / $total", 0);
      $this->__import_labels($data);
      if($this->__has_event()) {
        ++$this->event_put['total_labels'];
        $this->__update_event();
      }
    }
    
    return array($this->ordered_sequences, $this->labels);
  }
}