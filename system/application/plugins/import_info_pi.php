<?php

class ImportInfo
{
  private $ordered_sequences = array();
  private $labels = array();
  
  private $label_model = null;
  private $sequence_model = null;
  private $label_sequence_model = null;
  private $taxonomy_model = null;
  
  function ImportInfo()
  {
    $this->label_model = load_ci_model('label_model');
    $this->sequence_model = load_ci_model('sequence_model');
    $this->label_sequence_model = load_ci_model('label_sequence_model');
    $this->taxonomy_model = load_ci_model('taxonomy_model');
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
    $CI =& get_instance();
    $CI->load->library('SequenceExporter');
    
    $str = $CI->sequenceexporter->export_simple_fasta($this->ordered_sequences);
    
    return write_file_export($str);
  }
  
  public function convert_protein_file()
  {
    $fasta = $this->write_simple_fasta();
    
    $transeq = find_executable('transeq');
    if(!$transeq) {
      unlink($fasta);
      return null;
    }
    
    $protein = generate_new_file_name();
    
    exec("$transeq $fasta $protein", $cmdoutput, $ret);
    
    if($ret) {
      throw new Exception("Error executing transeq");  
    }
    
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
      
      if(!$this->label_model->has($name)) {
        $data['status'] = 'not_found';
      } else {
        $new_label = $this->label_model->get_by_name($name);
        
        if($new_label) {
          $data['status'] = 'ok';
          $data['data'] = $new_label;
          $data['type'] = $new_label['type'];
        } else {
          $data['status'] = 'not_found';
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
  
  private function __get_ref_value($seq_id, $name, $value)
  {
    $value = label_get_data($value);
    
    if($name == 'translated') {
      $type = $this->sequence_model->get_type($seq_id);
      
      if(!valid_sequence_type($type)) {
        return null;
      }
      
      $other_type = ($type == 'dna' ? 'protein' : 'dna');
      
      return $this->sequence_model->locate_sequence_type($value, $other_type);
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
  
  private function __update_label_content($id, $seq_id, $type, $name, $value)
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
        $data = $this->__get_ref_value($seq_id, $name, $value);
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
  
  private function __add_label_content($seq_id, $label_id, $label_name, $label_type, $value)
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
        $data = $this->__get_ref_value($seq_id, $label_name, $value);
        if($data) {
          return $model->add_ref_label($seq_id, $label_id, $data);
        } else {
          return null;
        }
      case 'tax':
        return $model->add_tax_label($seq_id, $label_id, $this->__get_tax_value($value));
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
    
    if($this_label == null) {
      $seq_labels[$label_name] = array('status' => 'Empty / Not inserted');
      return;
    }
    
    if($label_data['status'] != 'ok') {
      $this_label['status'] = 'Invalid';
      return;
    }
    
    $seq_id = $seq_data['id'];
    $label_info =& $label_data['data'];
    $label_id = $label_info['id'];
    $label_type = $label_info['type'];
    
    $already_there = $this->label_sequence_model->label_used_up($seq_id, $label_id);
    
    $editable = $label_info['editable'];
    $multiple = $label_info['multiple'];
    $values =& $this_label['values'];
    
    if($already_there && !$editable && !$multiple) {
      $this_label['status'] = 'Already inserted';
    } else if($already_there && $editable && !$multiple) {
      $id = $this->label_sequence_model->get_label_id($seq_id, $label_id);
      $value = $values[0];
      
      $text_natural = $this->__import_label_text_natural($value, $label_type);
      
      if($this->__update_label_content($id, $seq_id, $label_type, $label_name, $value)) {
        $this_label['status'] = "Updated value: $text_natural";
      } else {
        $this_label['status'] = "Parse error: $text_natural";
      }
    } else if(!$already_there) {
      if($label_info['auto_on_creation']) {
        $this_label['status'] = 'Generated';
        $this->label_sequence_model->add_auto_label($seq_id, $label_info);
      } else {
        
        if($multiple && count($values) > 1) {
          // this is a multiple value label
          $status_text = 'Values:';
          
          foreach($values as $value) {
            $text_natural = $this->__import_label_text_natural($value, $label_type);
            
            if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value)) {
              $status_text .= " ($text_natural, OK)";
            } else {
              $status_text .= " ($text_natural, ERROR)";
            }
          }
          
          $this_label['status'] = $status_text;
        } else {
          
          $this->__add_single_label($seq_id, $label_id, $label_name, $label_type, $this_label, $values[0]);
          
        }
      }
    }
  }
  
  private function __add_single_label($seq_id, $label_id, $label_name, $label_type, &$this_label, $value)
  {
    if($value == '') {
      $this_label['status'] = 'Empty / Not inserted';
      return;
    }
    
    $text_natural = $this->__import_label_text_natural($value, $label_type);
    if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value)) {
      $this_label['status'] = "Value: $text_natural";
    } else {
      $this_label['status'] = "Parse error: $text_natural";
    }
  }
  
  private function __import_labels(&$data)
  {
    $seq_name = $data['name'];
    //echo " => $seq_name ";
    foreach($this->labels as $name => &$label_data) {
      $this->__import_sequence_label($data, $name, $label_data);
    }
    //print_r($data['labels']);
    //echo "<br />";
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
    
    foreach($this->ordered_sequences as &$data) {
      $this->__import_labels($data);
    }
    
    return array($this->ordered_sequences, $this->labels);
  }
}