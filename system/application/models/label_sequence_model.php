<?php

class Label_sequence_model extends BioModel
{
  private static $label_data_fields = 'int_data, float_data, text_data, obj_data, ref_data, position_start, position_length, taxonomy_data, url_data, bool_data, DATE_FORMAT(date_data, "%d-%m-%Y") AS date_data, taxonomy_name, sequence_name';

  private static $label_basic_fields = "label_id, id, seq_id, history_id, `type`, `name`, `param`, `default`, must_exist, auto_on_creation, auto_on_modification, deletable, editable, multiple";
  
  function Label_sequence_model()
  {
    parent::BioModel('label_sequence');
  }

  private function __get_select()
  {
    return self::$label_basic_fields . ", " . self::$label_data_fields . ", update_user_id, `update`, user_name";
  }

  // get label instance id
  public function get_label_id($seq_id, $label_id)
  {
    $this->db->where('seq_id', $seq_id);
    return $this->get_id_by_field('label_id', $label_id);
  }
  
  // get label instance id by param
  private function get_label_param_id($seq_id, $label_id, $param)
  {
    $this->db->where('seq_id', $seq_id);
    $this->db->where('param', $param);
    
    return $this->get_id_by_field('label_id', $label_id);
  }

  // retrieve label instance row using seq id and label id
  public function get_label_info($seq_id, $label_id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $seq_id);
    return $this->get_row('label_id', $label_id, 'label_sequence_info');
  }
  
  // retrieve multiple label instances
  public function get_label_infos($seq_id, $label_id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $seq_id);
    $this->db->where('label_id', $label_id);
    
    return $this->get_all('label_sequence_info');
  }

  public function get($id)
  {
    $this->db->select($this->__get_select() . ", `code`", FALSE);
    return $this->get_id($id, 'label_sequence_info');
  }
  
  public function label_used_up($seq, $label)
  {
    $this->db
      ->where('seq_id', $seq)
      ->where('label_id', $label)
      ->where('multiple IS FALSE');

    return $this->count_total('label_sequence_info') > 0;
  }

  public function sequence_has_label($seq, $label)
  {
    return $this->num_label($seq, $label) > 0;
  }
  
  public function num_label($seq_id, $label_id)
  {
    $this->db
      ->where('seq_id', $seq_id)
      ->where('label_id', $label_id);
      
    return $this->count_total();
  }
  
  public function label_exists($id)
  {
    return $this->has_id($id);
  }
  
  public function has_label_param($label_id, $seq_id, $param)
  {
    $this->db->select('id');
    $this->db->where('label_id', $label_id);
    $this->db->where('seq_id', $seq_id);
    $this->db->where('param', $param);
    
    return $this->has_something();
  }
  
  public function has_label_data($label_id, $seq_id, $label_type, $label_data, $except_id = null)
  {
    $param = label_get_param($label_data);
    
    if($except_id) {
      $this->db->where("id <> $except_id");
    }
    
    if($param) {
      return $this->has_label_param($label_id, $seq_id, $param);
    } else {
      $field = label_data_fields($label_type);
      $this->db->select('id');
      $this->db->where('label_id', $label_id);
      $this->db->where('seq_id', $seq_id);
    
      $data = label_get_data($label_data);
    
      if(is_array($field)) {
        $field1 = $field[0];
        $field2 = $field[1];
        $this->db->where($field1, $data[0]);
        $this->db->where($field2, $data[1]);
      } else {
        $this->db->where($field, $data);
      }
      
      return $this->has_something();
    }
  }

  public function get_data($id)
  {
    $this->db->select('id, type, ' . self::$label_data_fields);

    $data = $this->get_id($id, 'label_sequence_info');

    return label_get_type_data($data);
  }
  
  public function get_instances_info($seq_id, $label_id, $only_public = false)
  {
    $label_model = $this->load_model('label_model');
    $name = $label_model->get_name($label_id);
    
    if(label_special_purpose($name)) {
      $seq_model = $this->load_model('sequence_model');
      $ret = array();
      
      switch($name) {
        case 'name':
          $value = $seq_model->get_name($seq_id);
          break;
        case 'content':
          $value = $seq_model->get_content($seq_id);
          break;
        case 'creation_user':
          $value = $seq_model->get_creation_user($seq_id);
          $ret['id'] = $seq_model->get_creation_user_id($seq_id);
          break;
        case 'update_user':
          $value = $seq_model->get_update_user($seq_id);
          $ret['id'] = $seq_model->get_update_user_id($seq_id);
          break;
        case 'creation_date':
          $value = $seq_model->get_creation_date($seq_id);
          break;
        case 'update_date':
          $value = $seq_model->get_update_date($seq_id);
          break;
        default:
          $value = '';
          break;
      }
      
      $ret['string'] = $value;
      
      return array($ret);
    }
    
    $this->db->select('id, param, `type`, ' . self::$label_data_fields, FALSE);
    $this->db->where('seq_id', $seq_id);
    $this->db->where('label_id', $label_id);
    if($only_public)
      $this->db->where('public IS TRUE');
    
    $rows = $this->get_all('label_sequence_info');
    
    if(count($rows) == 0)
      return null;
    
    $value = array();
    
    foreach($rows as &$row) {
      $new = array('string' => label_get_printable_string($row));
      
      switch($row['type']) {
        case 'ref':
          $new['id'] = $row['ref_data'];
          break;
        case 'tax':
          $new['id'] = $row['taxonomy_data'];
          break;
        case 'obj':
          $new['id'] = $row['id'];
          break;
      }
      
      $param = $row['param'];
      if($param) {
        $new['param'] = $param;
      }
      
      $value[] = $new;
    }
    
    return $value;
  }
  
  // locate sequence id which contains a label with data
  public function get_reverse_data($name_label, $data)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get_by_name($name_label);
    if(!$label) {
      return null;
    }
    $type = $label['type'];
    
    $this->db->select('seq_id');
    
    $fields = label_data_fields($type);
    
    if(is_array($fields)) {
      $this->db->where($fields[0], $data[0]);
      $this->db->where($fields[1], $data[1]);
    } else {
      $this->db->where($fields, $data);
    }
    
    $row = $this->get_row('name', $name_label, 'label_sequence_info');
    if(!$row) {
      return null;
    }
    
    return $row['seq_id'];
  }

  // retrieve the data field using the label name
  public function get_label($seq_id, $label_name)
  {
    $this->db->select('id, `type`, ' . self::$label_data_fields, FALSE);
    $this->db->where('name', $label_name);
    $this->db->where('seq_id', $seq_id);
    $all = $this->get_all('label_sequence_info');

    if(!$all || count($all) == 0) {
      return null;
    }

    return label_get_type_data($all[0]);
  }

  // get label by sequence and label id
  public function get_label_ids($seq_id, $label_id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $seq_id);

    return $this->get_row('label_id', $label_id, 'label_sequence_info');
  }

  // get all labels from a sequence
  public function get_sequence($id, $filtering = array())
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $id);
    $this->__filter($filtering);
    return $this->get_all('label_sequence_info');
  }
  
  private function __filter($filtering)
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if($name)
        $this->db->where("name REGEXP '$name'");
    }
    
    if(array_key_exists('type', $filtering)) {
      $type = $filtering['type'];
      if($type)
        $this->db->where('type', $type);
    }
    
    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
    }
  }
  
  // get labels from a set of sequences
  public function get_sequences($seqs)
  {
    $ret = array();
    
    foreach($seqs as &$seq) {
      $ret[] = $this->get_sequence($seq['id']);
    }
    
    return $ret;
  }
  
  public function add($seq, $label, $type, $data, $force_add = false)
  {
    $fields = label_data_fields($type);
    $multiple = $this->load_model('label_model')->is_multiple($label);
    
    label_fix_data($type, $data, $multiple);
    
    if(!label_validate_data($type, $data)) {
      return false;
    }
    
    $param = label_get_param($data);
    
    // if multiple and label instance with that
    // parameter is present, edit it instead of adding it
    if($multiple && $this->has_label_param($label, $seq, $param)) {
      $id = $this->get_label_param_id($seq, $label, $param);
      
      return $this->edit($id, $type, $data);
    }
    
    if(!$multiple) {
      // if not multiple and label instance is present
      // edit instead of adding
      $old_id = $this->get_label_id($seq, $label);
      if($old_id) {
        return $this->edit($old_id, $type, $data);
      }
    }
    
    // check already inserted data
    if(!$force_add && $this->has_label_data($label, $seq, $type, $data)) {
      return true;
    }
    
    if(!$this->label_is_valid($label, $seq, $data)) {
      return false;
    }

    $db_data = array(
      'seq_id' => $seq,
      'label_id' => $label,
      'param' => label_get_param($data)
    );

    $data_value = label_get_data($data);
    
    if(is_array($fields)) {
      $db_data[$fields[0]] = $data_value[0];
      $db_data[$fields[1]] = $data_value[1];
    } else {
      $db_data[$fields] = $data_value;
    }

    return $this->insert_data_with_history($db_data) ? TRUE : FALSE;
  }

  public function add_generated_label($seq_id, $label_id, $type = null)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if(($type == null || $label['type'] == $type) && $label['code']) {
      return $this->add_auto_label($seq_id, $label);
    } else {
      return false;
    }
  }
  
  // remove all label instances of a certain label from a sequence
  public function remove_labels_sequence($label_id, $seq_id)
  {
    $this->db->where('seq_id', $seq_id);
    $this->db->where('label_id', $label_id);
    
    return $this->delete_rows();
  }
  
  public function add_auto_label($seq, $label)
  {
    $type = $label['type'];
    $label_id = $label['id'];
    $res = $this->generate_label_value($seq, $label['code']);
    
    if($label['multiple']) {
      $ret = 0;
      
      if(!$label['editable']) {
        $this->remove_labels_sequence($label_id, $seq);
      }
      
      if($res == null) {
        return false;
      }
      
      $force_add = !$label['editable'];
      
      if(!is_array($res)) {
        return 0;
      }
      
      foreach($res as &$data) {
        $ret = $ret + ($this->add($seq, $label_id, $type, $data, $force_add) ? 1 : 0);
      }
      
      return $ret;
    } else {
      return $this->add($seq, $label_id, $type, $res) ? 1 : 0;
    }
  }

  public function add_auto_label_id($seq, $label_id)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    return $this->add_auto_label($seq, $label);
  }

  public function edit($id, $type, $value)
  {
    $fields = label_data_fields($type);
    $label_info = $this->get($id);
    $multiple = $label_info['multiple'];

    label_fix_data($type, $value, $multiple);
    
    if(!label_validate_data($type, $value)) {
      return false;
    }
    
    $param = label_get_param($value);
    $label_id = $label_info['label_id'];
    $seq_id = $label_info['seq_id'];
    
    if($multiple && $this->has_label_data($label_id, $seq_id, $type, $value, $id)) {
      return false;
    }
    
    if(!$this->label_is_valid($label_id, $seq_id, $value)) {
      return false;
    }
    
    $data = array();
    $data_value = label_get_data($value);
    
    if(is_array($fields)) {
      $data[$fields[0]] = $data_value[0];
      $data[$fields[1]] = $data_value[1];
    } else {
      $data[$fields] = $data_value;
    }
  
    if($param)
      $data['param'] = $param;
      
    return $this->edit_data_with_history($id, $data);
  }

  public function add_initial_labels($id)
  {
    $label_model = $this->load_model('label_model');
    $labels = $label_model->get_to_add();
    $ret = true;

    foreach($labels as $label)
    {
      if(!$this->add_initial_label($id, $label)) {
        $ret = false;
      }
    }
    
    return $ret;
  }

  public function generate_label_value($id, $code)
  {
    $seq_model = $this->load_model('sequence_model');

    $seq = $seq_model->get_id($id);

    // once the sequence has been fetched it can be used in the label code!
    $name = $seq['name'];
    $content = $seq['content'];
    
    try {
      return eval($code);
    } catch(Exception $e) {
      return null;
    }
  }

  public function add_initial_label($id, $label)
  {
    if($label['code'] && $label['auto_on_creation']) {
      return $this->add_auto_label($id, $label);
    }
  }

  public function edit_auto_label($id)
  {
    $label = $this->get($id);
    return $this->regenerate_label($label['label_id'], $label['seq_id']);
  }
  
  private function __is_date($label)
  {
    return $label['type'] == 'date';
  }
  
  // $date: dd-mm-yyyy hh:mm:ss
  public function add_date_label($seq_id, $label_id, $date)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);
    
    if($this->__is_date($label)) {
      return $this->add($seq_id, $label_id, 'date', $date);
    } else {
      return false;
    }
  }
  
  public function edit_date_label($id, $date)
  {
    $label = $this->get($id);

    if($this->__is_date($label)) {
      return $this->edit($id, 'date', $date);
    } else {
      return false;
    }
  }

  private function __is_text($label)
  {
    return $label['type'] == 'text';
  }

  public function add_text_label($seq_id, $label_id, $text)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_text($label)) {
      return $this->add($seq_id, $label_id, 'text', $text);
    } else {
      return false;
    }
  }

  public function edit_text_label($id, $text)
  {
    $label = $this->get($id);

    if($this->__is_text($label)) {
      return $this->edit($id, 'text', $text);
    } else {
      return false;
    }
  }

  private function __is_integer($label)
  {
    return $label['type'] == 'integer';
  }

  public function add_integer_label($seq_id, $label_id, $int)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_integer($label)) {
      return $this->add($seq_id, $label_id, 'integer', $int);
    } else {
      return false;
    }
  }

  public function edit_integer_label($id, $int)
  {
    $label = $this->get($id);

    if($this->__is_integer($label)) {
      return $this->edit($id, 'integer', $int);
    } else {
      return false;
    }
  }
  
  private function __is_float($label)
  {
    return $label['type'] == 'float';
  }
  
  public function add_float_label($seq_id, $label_id, $float)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_float($label)) {
      return $this->add($seq_id, $label_id, 'float', $float);
    } else {
      return false;
    }
  }
  
  public function edit_float_label($id, $float)
  {
    $label = $this->get($id);

    if($this->__is_float($label)) {
      return $this->edit($id, 'float', $float);
    } else {
      return false;
    }
  }

  private function __is_bool($label)
  {
    return $label['type'] == 'bool';
  }

  public function add_bool_label($seq_id, $label_id, $bool)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_bool($label)) {
      return $this->add($seq_id, $label_id, 'bool', $bool);
    } else {
      return false;
    }
  }

  public function edit_bool_label($id, $bool)
  {
    $label = $this->get($id);

    if($this->__is_bool($label)) {
      return $this->edit($id, 'bool', $bool);
    } else {
      return false;
    }
  }

  private function __is_url($label)
  {
    return $label['type'] == 'url';
  }

  public function add_url_label($seq_id, $label_id, $url)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_url($label)) {
      return $this->add($seq_id, $label_id, 'url', $url);
    } else {
      return false;
    }
  }

  public function edit_url_label($id, $url)
  {
    $label = $this->get($id);

    if($this->__is_url($label)) {
      return $this->edit($id, 'url', $url);
    } else {
      return false;
    }
  }

  private function __is_obj($label)
  {
    return $label['type'] == 'obj';
  }

  public function add_obj_label($seq_id, $label_id, $filename, $data, $param = null)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_obj($label)) {
      return $this->add($seq_id, $label_id, 'obj', new LabelData(array($filename, $data), $param));
    } else {
      return false;
    }
  }

  public function edit_obj_label($id, $filename, $data, $param = null)
  {
    $label = $this->get($id);

    if($this->__is_obj($label)) {
      return $this->edit($id, 'obj', new LabelData(array($filename, $data), $param));
    } else {
      return false;
    }
  }

  private function __is_ref($label)
  {
    return $label['type'] == 'ref';
  }

  public function add_ref_label($seq_id, $label_id, $ref)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_ref($label)) {
      $ret = $this->add($seq_id, $label_id, 'ref', $ref);
      if($ret && $label['name'] == 'translated') {
        $this->ensure_translated_label($label_id, $ref, $seq_id);
      }
      return $ret;
    } else {
      return false;
    }
  }
  
  public function ensure_translated_label($translated_id, $id, $ref)
  {
    $label_data = $this->get_label_info($id, $translated_id);
    
    if($label_data) {
      if($label_data['ref_data'] != $ref) {
        $this->edit_ref_label($label_data['id'], $ref);
      }
    } else {
      $ret = $this->add_ref_label($id, $translated_id, $ref);
    }
  }

  public function edit_ref_label($id, $ref)
  {
    $label = $this->get($id);

    if($this->__is_ref($label)) {
      $ret = $this->edit($id, 'ref', $ref);
      if($ret && $label['name'] == 'translated') {
        $this->ensure_translated_label($label['label_id'], $ref, $label['seq_id']);
      }
      return $ret;
    } else {
      return false;
    }
  }

  private function __is_tax($label)
  {
    return $label['type'] == 'tax';
  }

  public function add_tax_label($seq_id, $label_id, $tax)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_tax($label)) {
      $this->add($seq_id, $label_id, 'tax', $tax);
      return true;
    } else {
      return false;
    }
  }

  public function edit_tax_label($id, $tax)
  {
    $label = $this->get($id);

    if($this->__is_tax($label)) {
      return $this->edit($id, 'tax', $tax);
    } else {
      return false;
    }
  }

  private function __is_position($label)
  {
    return $label['type'] == 'position';
  }

  public function add_position_label($seq_id, $label_id, $start, $length, $param = null)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_position($label)) {
      return $this->add($seq_id, $label_id, 'position', new LabelData(array($start, $length), $param));
    } else {
      return false;
    }
  }

  public function edit_position_label($id, $start, $length, $param = null)
  {
    $label = $this->get($id);

    if($this->__is_position($label)) {
      return $this->edit($id, 'position', new LabelData(array($start, $length), $param));
    } else {
      return false;
    }
  }

  private function get_labels_to_auto_modify($seq)
  {
    $this->db->select('label_id');
    $this->db->distinct();
    $this->db->where('seq_id', $seq);
    $this->db->where('auto_on_modification', true);
    $this->__filter_special_labels();

    return $this->get_all('label_sequence_info');
  }
  
  private function run_modification_action($seq_id, $label_id)
  {
    $label_model = $this->load_model('label_model');
    $label = $this->label_model->get($label_id);
    
    if(!$label['action_modification'] || $label['action_modification'] == '') {
      return null;
    }
  
    $sequence_model = $this->load_model('sequence_model');
    $sequence = $this->sequence_model->get($seq_id);
    
    $label_name = $label['name'];
    $label_id = $label['id'];
    $code = $label['action_modification'];
    $sequence_id = $sequence['id'];
    $content = $sequence['content'];
    
    try {
      eval($code);
      return true;
    } catch(Exception $e) {
      return null;
    }
  }
  
  public function run_modification_actions($seq)
  {
    $labels = $this->get_labels_with_action_modification($seq);
    
    foreach($labels as &$label) {
      $label_id = $label['label_id'];
      
      $this->run_modification_action($seq, $label_id);
    }
  }
  
  private function get_labels_with_action_modification($seq)
  {
    $this->db->select('label_id');
    $this->db->distinct();
    $this->db->where('seq_id', $seq);
    $this->db->where('action_modification IS NOT NULL');
    $this->__filter_special_labels();
    
    return $this->get_all('label_sequence_info');
  }

  public function regenerate_label($label_id, $seq_id)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);
    
    $type = $label['type'];
    $code = $label['code'];
    $value = $this->generate_label_value($seq_id, $code);
    
    if($label['multiple']) {
      $ret = false;
      
      if(!$label['editable']) {
        // this is a multiple generated label
        $this->remove_labels_sequence($label_id, $seq_id);
      }
      
      foreach($value as &$data) {
        $ret = $this->add($seq_id, $label_id, $type, $data) || $ret;
      }
      
      return $ret;
    } else {
      $id = $this->get_label_id($seq_id, $label_id);
      return $this->edit($id, $type, $value);
    }
  }

  public function regenerate_labels($seq)
  {
    $labels = $this->get_labels_to_auto_modify($seq);
    $this->db->trans_start();
    $ret = true;
    
    foreach($labels as &$label) {
      $label_id = $label['label_id'];
      if(!$this->regenerate_label($label_id, $seq)) {
        $ret = false;
      }
    }

    $this->db->trans_complete();
    
    return $ret;
  }

  public function total_label($id)
  {
    $this->db->where('label_id', $id);

    return $this->count_total();
  }

  public function delete($id)
  {
    $info = $this->get_id($id, 'label_sequence_info');

    if(!$info['deletable']) {
      return false;
    }

    return $this->delete_id($id);
  }
  
  public function delete_label_seq($seq_id, $label_id)
  {
    $this->db->where('seq_id', $seq_id);
    $this->db->where('label_id', $label_id);
    return $this->delete_rows();
  }

  public function get_obligatory($id)
  {
    $this->db->distinct();
    $this->db->select('label_id');
    $this->db->where('seq_id', $id);
    $this->db->where('must_exist', TRUE);
    $this->__filter_special_labels();

    $labels = $this->get_all('label_sequence_info');

    $ret = array();

    foreach($labels as $label) {
      $ret[] = intval($label['label_id']);
    }

    return $ret;
  }

  public function get_missing_obligatory_ids($id)
  {
    $label_model = $this->load_model('label_model');
    $all = $label_model->get_obligatory();
    $exist = $this->get_obligatory($id);

    $ret = array();

    foreach($all as $item) {
      if(!in_array($item, $exist)) {
        $ret[] = $item;
      }
    }

    return $ret;
  }

  public function get_missing_obligatory($id)
  {
    $ids = $this->get_missing_obligatory_ids($id);
    $label_model = $this->load_model('label_model');

    $ret = array();
    foreach($ids as $id) {
      $ret[] = $label_model->get_simple($id);
    }

    return $ret;
  }

  public function has_missing($id)
  {
    return count($this->get_missing_obligatory($id)) > 0;
  }

  public function get_addable_labels($id, $filtering = array())
  {
    if(!is_numeric($id)) {
      return false;
    }
    
    $sql = "SELECT label_id AS id, name, type, must_exist, auto_on_creation,
          auto_on_modification, deletable, editable, multiple
      FROM label_info_history
      WHERE name <> 'name'
            AND name <> 'content'
            AND name <> 'creation_user'
            AND name <> 'update_user'
            AND name <> 'creation_date'
            AND name <> 'update_date'
            AND ((multiple IS TRUE AND editable IS TRUE)
                  OR
                    label_id NOT IN (SELECT DISTINCT label_id
                              FROM label_sequence
                              WHERE seq_id = $id))";
                              
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if($name)
        $sql .= " AND name REGEXP '$name'";
    }
    
    if(array_key_exists('type', $filtering)) {
      $type = $filtering['type'];
      if($type)
        $sql .= " AND type = '$type'";
    }
    
    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $sql .= " AND update_user_id = $user";
      }
    }
    
    $sql .= " ORDER BY name";
      
    return $this->rows_sql($sql);
  }

  private function __get_validation_status($label, $sequence, $valid_code, $data)
  {
    $seq_model = $this->load_model('sequence_model');
    $seq_id = $sequence['id'];
    $content = $sequence['content'];
    $name = $sequence['name'];
    $data = label_get_data($data);

    $ret = eval($valid_code);

    if($ret) {
      return 'valid';
    } else {
      return 'invalid';
    }
  }

  public function get_validation_status($label_id, $sequence, &$data)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    $valid_code = $label['valid_code'];

    if(!$valid_code) {
      return 'no validation';
    }

    return $this->__get_validation_status($label, $sequence, $valid_code, $data);
  }
  
  // returns true if label data of label label_id is valid in context of the sequence sequence_id
  public function label_is_valid($label_id, $sequence_id, $data)
  {
    $seq_model = $this->load_model('sequence_model');
    
    $result = $this->get_validation_status($label_id, $seq_model->get($sequence_id), $data);
    
    return $result != 'invalid';
  }

  private function __bad_multiple_sql($id)
  {
    if(!is_numeric($id)) {
      return null;
    }
    
    return "FROM label_sequence_info AS lsi
      WHERE multiple IS FALSE AND
            label_id IN (SELECT label_id
                         FROM label_sequence AS ls
                         WHERE ls.id <> lsi.id AND
                           ls.label_id = lsi.label_id
                           AND ls.seq_id = $id)
           AND seq_id = $id";
  }

  public function get_bad_multiple($id)
  {
    $sql = "SELECT id, label_id, seq_id, name, type, " . self::$label_data_fields . " " .
        $this->__bad_multiple_sql($id);

    return $this->rows_sql($sql);
  }

  public function has_bad_multiple($id)
  {
    $sql = "SELECT count(id) AS total  " .
      $this->__bad_multiple_sql($id);

    return $this->total_sql($sql) > 0;
  }

  public function count_taxonomies($tax)
  {
    $this->db->select('id');
    $this->db->where('taxonomy_data', $tax);
    return $this->count_total();
  }

  public function count_sequences_for_label($label)
  {
    $this->db->select('DISTINCT id');
    $this->db->where('label_id', $label);
    return $this->count_total();
  }

  // get all unique label ids from this sequence
  public function get_sequence_labels($id)
  {
    $this->db->select('label_id AS id');
    $this->db->distinct();
    $this->db->where('seq_id', $id);

    return $this->get_all();
  }

  // get all labels that are in the sequences set
  public function get_all_labels($seqs)
  {
    $labelids = array();
    $label_model = $this->load_model('label_model');
    $labeldata = array();

    foreach($seqs as $seq) {
      $id = $seq['id'];

      $all = $this->get_sequence_labels($id);
      $labelids[] = $all;

      foreach($all as $label) {
        $label_id = $label['id'];

        if(!array_key_exists($label_id, $labeldata)) {
          $label = $label_model->get($label_id);
          $labeldata[$label_id] = $label;
        }
      }
    }

    foreach($labeldata as $id => &$label) {
      $in_all = $this->__find_all_seqs($labelids, $id);
      $label['intersection'] = $in_all;
    }

    return $labeldata;
  }

  // find label id in label ids set
  private function __find_all_seqs($labelids, $id) {
    foreach($labelids as &$seqlabels) {
      $found = false;

      foreach($seqlabels as &$label) {
        $this_id = $label['id'];

        if($this_id == $id) {
          $found = true;
          break;
        }
      }

      if(!$found) {
        return false;
      }
    }

    return true;
  }
}