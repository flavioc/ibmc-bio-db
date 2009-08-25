<?php

class ImportInfo
{
  private $sequences = array();
  private $ordered_sequences = array();
  private $sequence_labels = array();
  private $labels = array();
  private $controller = null;
  
  function ImportInfo($ctr = null)
  {
    $this->controller = $ctr; 
  }
  
  public function duo_match($info2)
  {
    $total1 = count($this->ordered_sequences);
    $total2 = count($info2->ordered_sequences);
    
    return $total1 == $total2;
  }
  
  public function all_type($wanted_type)
  {
    foreach($this->sequences as &$seq) {
      $content =& $seq['content'];
      $type = sequence_type($content);
      
      if($type != $wanted_type) {
        return false;
      }
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
      
      $this->controller->sequence_model->set_translated_sequence($dna_id, $protein_id);
    }
  }
  
  // writes imported sequences to a temporary fasta file returning the temporary filename
  public function write_simple_fasta()
  {
    $this->controller->load->helper('exporter');
    $str = export_sequences_simple($this->sequences);
    return __write_fasta_file_export($str);
  }
  
  public function convert_protein_file()
  {
    $fasta = $this->write_simple_fasta();
    
    $transeq = find_executable('transeq');
    if(!$transeq) {
      unlink($fasta);
      return null;
    }
    
    $protein = generate_new_file_name_export();
    
    shell_exec("$transeq $fasta $protein");
    
    unlink($fasta);
    
    return $protein;
  }
  
  public function add_label($name, $type)
  {
    $this->labels[$name] = array('type' => $type);
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
    $name = trim($name);
    $this->sequences[$name] = array('name' => $name, 'content' => sequence_normalize($content), 'id' => $id);
    $this->sequence_labels[$name] = array();
    $this->sequences[$name]['labels'] =& $this->sequence_labels[$name];
    
    $this->ordered_sequences[] =& $this->sequences[$name];
  }
  
  public function add_sequence_label($sequence, $label, $value)
  {
    $sequence = trim($sequence);
    $label = trim($label);
    
    if(!array_key_exists($sequence, $this->sequence_labels)) {
      return false;
    }
    
    if(!array_key_exists($label, $this->labels)) {
      return false;
    }
    
    $labels =& $this->sequence_labels[$sequence];
    
    if(array_key_exists($label, $labels)) {
      $current_label_info =& $labels[$label];
      $values =& $current_label_info['values'];
      $values[] = $value;
    } else {
      $labels[$label] = array('values' => array($value));
    }
    
    return true;
  }
  
  private function print_stats()
  {
    foreach($this->sequence_labels as $name => &$labels)
    {
      echo "SEQUENCE: $name ";
      foreach($labels as $label => &$data) {
        echo "$label -> ";
        foreach($data['values'] as $value) {
          echo "$value ";
        }
      }
      echo "<br />";
    }
  }
  
  private function __get_labels()
  {
    foreach($this->labels as $name => &$data) {
      if(!$this->controller->label_model->has($name)) {
        $data['status'] = 'not_found';
      } else {
        $new_label = $this->controller->label_model->get_by_name($name);
        
        if($new_label) {
          if($new_label['type'] != $data['type']) {
            $data['status'] = 'type_differ';
            $data['new_type'] = $new_label['type'];
          } else {
            $data['status'] = 'ok';
            $data['data'] = $new_label;
          }
        } else {
          $data['status'] = 'not_found';
        }
      }
    }
  }
  
  private function __import_label_text_natural($value, $type)
  {
    switch($type) {
    case 'bool':
      return $value == '0' ? 'No' : 'Yes';
    }

    return $value;
  }
  
  private function __get_ref_value($seq_id, $name, $value)
  {
    
    if($name == 'translated') {
      $type = $this->controller->sequence_model->get_type($seq_id);
      
      if(!valid_sequence_type($type)) {
        return null;
      }
      
      $other_type = ($type == 'dna' ? 'protein' : 'dna');
      
      return $this->controller->sequence_model->locate_sequence_type($value, $other_type);
    } else {
      $all = $this->controller->sequence_model->locate_all($value);
      
      if(count($all) > 0) {
        return $all[0];
      }
      
      return null;
    }
    
    return $value;
  }
  
  private function __get_tax_value($value)
  {
    $row = $this->controller->taxonomy_model->get_by_name($value);
    return $row['id'];
  }
  
  private function __update_label_content($id, $seq_id, $type, $name, $value)
  {
    if($value == '') {
      return;
    }
    
    $model = $this->controller->label_sequence_model;
    
    switch($type) {
      case 'integer':
        return $model->edit_integer_label($id, $value);
      case 'text':
        return $model->edit_text_label($id, $value);
      case 'position':
        $vec = explode(' ', $value);
        if(count($vec) != 2) {
          return false;
        }
        return $model->edit_position_label($id, $vec[0], $vec[1]);
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
    $model = $this->controller->label_sequence_model;

    switch($label_type) {
      case 'integer':
        return $model->add_integer_label($seq_id, $label_id, $value);
      case 'text':
        return $model->add_text_label($seq_id, $label_id, $value);
      case 'position':
        $vec = explode(' ', $value);
        if(count($vec) != 2) {
          return false;
        }
        return $model->add_position_label($seq_id, $label_id, $vec[0], $vec[1]);
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
  
  private function __import_sequence_label($seq_name, &$seq_data, $label_name, &$label_data)
  {
    $seq_labels =& $this->sequence_labels[$seq_name];
    $this_label =& $seq_labels[$label_name];
    
    if($this_label == null) {
      $this_label['status'] = 'Empty / Not inserted';
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
    
    $already_there = $this->controller->label_sequence_model->label_used_up($seq_id, $label_id);
    $editable = $label_info['editable'];
    $multiple = $label_info['multiple'];
    $values =& $this_label['values'];
    
    if($already_there && !$editable && !$multiple) {
      $this_label['status'] = 'Already inserted';
    } else if($already_there && $editable && !$multiple) {
      $id = $this->controller->label_sequence_model->get_label_id($seq_id, $label_id);
      $value = $values[0];
      
      if($this->__update_label_content($id, $seq_id, $label_type, $label_name, $value)) {
        $text_natural = $this->__import_label_text_natural($value, $label_type);
        $this_label['status'] = "Updated value: $text_natural";
      } else {
        $this_label['status'] = "Parse error: $value";
      }
    } else if(!$already_there) {
      if($label_info['auto_on_creation'] && $label_info['code']) {
        $this_label['status'] = 'Generated';
        $this->controller->label_sequence_model->add_auto_label($seq_id, $label_info);
      } else {
        
        if($multiple && count($values) > 1) {
          // this is a multiple value label
          $status_text = 'Values:';
          
          foreach($values as $value) {
            if($value == '') {
              continue;
            }
            if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value)) {
              $text_natural = $this->__import_label_text_natural($value, $label_type);
              $status_text .= " ($text_natural, OK)";
            } else {
              $status_text .= " ($value, ERROR)";
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
    
    if($this->__add_label_content($seq_id, $label_id, $label_name, $label_type, $value)) {
      $text_natural = $this->__import_label_text_natural($value, $label_type);
      $this_label['status'] = "Value: $text_natural";
    } else {
      $this_label['status'] = "Parse error: $value";
    }
  }
  
  private function __import_labels($seq_name, &$data)
  {
    foreach($this->labels as $name => &$label_data) {
      $this->__import_sequence_label($seq_name, $data, $name, $label_data);
    }
  }
  
  public function import()
  {
    $this->controller->load->model('label_model');
    $this->controller->load->model('label_sequence_model');
    $this->controller->load->model('sequence_model');
    
    $this->__get_labels();
    
    foreach($this->sequences as $name => &$data) {
      $content = $data['content'];
      $seq_labels =& $this->sequence_labels[$name];
      $isnew = false;
      
      if($this->controller->sequence_model->has_same_sequence($name, $content)) {
        $data['id'] = $this->controller->sequence_model->get_id_by_name_and_content($name, $content);
        $data['content'] = $this->controller->sequence_model->get_content($data['id']);
        $data['comment'] = 'Sequence name and content are identical.';
      } else {
        $isnew = true;
        $data['comment'] = '';
        $data['id'] = $this->controller->sequence_model->add($name, $content);
      }
      
      $data['short_content'] = sequence_short_content($data['content']);
      $data['add'] = $isnew;
      $this->__import_labels($name, $data);
    }
    
    return array($this->sequences, $this->labels);
  }
}