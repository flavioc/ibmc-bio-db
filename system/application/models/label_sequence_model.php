<?php

class Label_sequence_model extends BioModel
{
  private static $label_data_fields = 'int_data, float_data, text_data, obj_data, ref_data, position_start, position_length, taxonomy_data, url_data, bool_data, DATE_FORMAT(date_data, "%d-%m-%Y") AS date_data, taxonomy_name, sequence_name';

  private static $label_basic_fields = "label_id, id, seq_id, history_id, `type`, `name`, `default`, must_exist, auto_on_creation, auto_on_modification, deletable, editable, multiple";
  
  private static $public_sequence_where = "EXISTS (SELECT * FROM label_sequence_info WHERE label_sequence_info.seq_id = sequence_info_history.id AND label_sequence_info.name = 'perm_public' AND label_sequence_info.bool_data IS TRUE)";

  function Label_sequence_model()
  {
    parent::BioModel('label_sequence');
  }

  private function __get_select()
  {
    return self::$label_basic_fields . ", " . self::$label_data_fields . ", update_user_id, `update`, user_name";
  }

  public function get_label_id($seq_id, $label_id)
  {
    $this->db->where('seq_id', $seq_id);
    return $this->get_id_by_field('label_id', $label_id);
  }

  // retrieve label sequence row using seq id and label id
  public function get_label_info($seq_id, $label_id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $seq_id);
    return $this->get_row('label_id', $label_id, 'label_sequence_info');
  }

  public function get($id)
  {
    $this->db->select($this->__get_select() . ", `code`", FALSE);
    return $this->get_id($id, 'label_sequence_info');
  }

  // get all labels from a sequence
  public function get_sequence($id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $id);
    return $this->get_all('label_sequence_info');
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
  
  private function __validate_label_data($type, $data1, $data2)
  {
    switch($type) {
      case 'integer':
        return isint($data1);
      case 'float':
        return is_numeric($data1);
      case 'url':
        return strlen($data1) <= 2048 && (parse_url($data1) ? TRUE : FALSE);
      case 'text':
        $len = strlen($data1);
        return $len > 0 && $len <= 1024;
      case 'bool':
        return true;
      case 'obj':
        $len = strlen($data1);
        return $len > 0 && $len <= 1024;
      case 'position':
        if(!is_numeric($data1) || is_numeric($data2)) {
          return false;
        }
        $start = intval($data1);
        $length = intval($data2);
        return $start >= 0 && $length > 0;
      case 'ref':
        $seq_model = $this->load_model('sequence_model');
        if(!is_numeric($data1)) {
          return false;
        }
        $num = intval($data1);
        return $num > 0 &&
          $seq_model->has_sequence($num);
      case 'tax':
        if(!is_numeric($data1)) {
          return false;
        }
        $num = intval($data1);
        $tax_model = $this->load_model('taxonomy_model');
        return $num > 0 &&
          $tax_model->has_taxonomy($num);
      case 'date':
        return $data1 != null;
    }
    
    return false;
  }
  
  private function __fix_data($type, &$data1, &$data2)
  {
    switch($type) {
      case 'integer':
      case 'float':
      case 'position':
      case 'bool':
      case 'ref':
      case 'tax':
        return;
      case 'text':
      case 'url':
      case 'obj':
        $data1 = trim($data1);
        return;
      case 'date':
        $data1 = convert_html_date_to_sql(trim($data1));
        return;
    }
  }

  public function add($seq, $label, $type, $data1 = null, $data2 = null)
  {
    $fields = $this->__get_data_fields($type);
    $this->__fix_data($type, $data1, $data2);
    
    if(!$this->__validate_label_data($type, $data1, $data2)) {
      return false;
    }
    
    if(!$this->label_is_valid($label, $seq, $data1, $data2)) {
      return false;
    }
    
    // check already inserted data
    if($this->has_label_data($label, $seq, $type, $data1, $data2)) {
      return true;
    }
    
    $data = array(
      'seq_id' => $seq,
      'label_id' => $label
    );

    if(is_array($fields)) {
      $data[$fields[0]] = $data1;
      $data[$fields[1]] = $data2;
    } else {
      $data[$fields] = $data1;
    }

    if($this->insert_data_with_history($data)) {
      return true;
    }
    
    return false;
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
  
  
  public function add_auto_label($seq, $label)
  {
    $res = $this->generate_label_value($seq, $label['code']);
    
    $data1 = null;
    $data2 = null;

    if(is_array($res)) {
      $data1 = $res[0];
      $data2 = $res[1];
    } else {
      $data1 = $res;
    }

    return $this->add($seq, $label['id'], $label['type'], $data1, $data2);
  }

  public function add_auto_label_id($seq, $label_id)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    return $this->add_auto_label($seq, $label);
  }

  public function edit($id, $type, $data1 = null, $data2 = null)
  {
    $fields = $this->__get_data_fields($type);

    $this->__fix_data($type, $data1, $data2);
    
    if(!$this->__validate_label_data($type, $data1, $data2)) {
      return false;
    }
    
    $label_info = $this->get($id);
    if(!$this->label_is_valid($label_info['label_id'], $label_info['seq_id'], $data1, $data2)) {
      return false;
    }
    
    $data = array();

    if(is_array($fields)) {
      $data[$fields[0]] = $data1;
      $data[$fields[1]] = $data2;
    } else {
      $data[$fields] = $data1;
    }

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
    return $this->regenerate_label($label);
  }
  
  private function __is_date($label)
  {
    return $label['type'] == 'date';
  }
  
  public function add_date_label($seq_id, $label_id, $date)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);
    
    if($this->__is_date($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'date', $date);
    } else {
      return false;
    }
  }
  
  public function edit_date_label($id, $date)
  {
    $label = $this->get($id);

    if($this->__is_date($label) && $label['editable']) {
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

    if($this->__is_text($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'text', $text);
    } else {
      return false;
    }
  }

  public function edit_text_label($id, $text)
  {
    $label = $this->get($id);

    if($this->__is_text($label) && $label['editable']) {
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

    if($this->__is_integer($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'integer', $int);
    } else {
      return false;
    }
  }

  public function edit_integer_label($id, $int)
  {
    $label = $this->get($id);

    if($this->__is_integer($label) && $label['editable']) {
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

    if($this->__is_float($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'float', $float);
    } else {
      return false;
    }
  }
  
  public function edit_float_label($id, $float)
  {
    $label = $this->get($id);

    if($this->__is_float($label) && $label['editable']) {
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

    if($this->__is_bool($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'bool', $bool);
    } else {
      return false;
    }
  }

  public function edit_bool_label($id, $bool)
  {
    $label = $this->get($id);

    if($this->__is_bool($label) && $label['editable']) {
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

    if($this->__is_url($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'url', $url);
    } else {
      return false;
    }
  }

  public function edit_url_label($id, $url)
  {
    $label = $this->get($id);

    if($this->__is_url($label) && $label['editable']) {
      return $this->edit($id, 'url', $url);
    } else {
      return false;
    }
  }

  private function __is_obj($label)
  {
    return $label['type'] == 'obj';
  }

  public function add_obj_label($seq_id, $label_id, $filename, $data)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_obj($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'obj', $filename, $data);
    } else {
      return false;
    }
  }

  public function edit_obj_label($id, $filename, $data)
  {
    $label = $this->get($id);

    if($this->__is_obj($label) && $label['editable']) {
      return $this->edit($id, 'obj', $filename, $data);
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

    if($this->__is_ref($label) && $label['editable']) {
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

    if($this->__is_ref($label) && $label['editable']) {
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

    if($this->__is_tax($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'tax', $tax);
      return true;
    } else {
      return false;
    }
  }

  public function edit_tax_label($id, $tax)
  {
    $label = $this->get($id);

    if($this->__is_tax($label) && $label['editable']) {
      return $this->edit($id, 'tax', $tax);
    } else {
      return false;
    }
  }

  private function __is_position($label)
  {
    return $label['type'] == 'position';
  }

  public function add_position_label($seq_id, $label_id, $start, $length)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_position($label) && $label['editable']) {
      return $this->add($seq_id, $label_id, 'position', $start, $length);
    } else {
      return false;
    }
  }

  public function edit_position_label($id, $start, $length)
  {
    $label = $this->get($id);

    if($this->__is_position($label) && $label['editable']) {
      return $this->edit($id, 'position', $start, $length);
    } else {
      return false;
    }
  }

  public function get_labels_to_auto_modify($seq)
  {
    $this->db->where('auto_on_modification', true);
    $this->db->where('seq_id', $seq);
    $this->__filter_special_labels();

    return $this->get_all('label_sequence_info');
  }

  public function regenerate_label($label)
  {
    $id = $label['id'];
    $seq = $label['seq_id'];
    $type = $label['type'];
    $code = $label['code'];
    $value = $this->generate_label_value($seq, $code);
    
    $data1 = null;
    $data2 = null;

    if(is_array($value)) {
      $data1 = $value[0];
      $data2 = $value[1];
    } else {
      $data1 = $value;
    }

    return $this->edit($id, $type, $data1, $data2);
  }

  public function regenerate_labels($seq)
  {
    $labels = $this->get_labels_to_auto_modify($seq);

    $this->db->trans_start();
    $ret = true;
    
    foreach($labels as &$label) {
      if(!$this->regenerate_label($label)) {
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

  public function get_addable_labels($id)
  {
    if(!is_numeric($id)) {
      return false;
    }
    
    return $this->rows_sql("SELECT id, name, type, must_exist, auto_on_creation,
          auto_on_modification, deletable, editable, multiple
      FROM label
      WHERE multiple IS TRUE OR
            id NOT IN (SELECT DISTINCT label_id AS id
                      FROM label_sequence
                      WHERE seq_id = $id)
            AND name <> 'name'
            AND name <> 'content'
            AND name <> 'creation_user'
            AND name <> 'update_user'
            AND name <> 'creation_date'
            AND name <> 'update_date'");
  }

  private function __get_validation_status($label, $sequence, $valid_code, $data1, $data2)
  {
    $seq_model = $this->load_model('sequence_model');
    $seq_id = $sequence['id'];
    $content = $sequence['content'];
    $name = $sequence['name'];
    $data = $data1;

    $ret = eval($valid_code);

    if($ret) {
      return 'valid';
    } else {
      return 'invalid';
    }
  }

  public function get_validation_status($label_id, $sequence, $data1, $data2 = null)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    $valid_code = $label['valid_code'];

    if(!$valid_code) {
      return 'no validation';
    }

    return $this->__get_validation_status($label, $sequence, $valid_code, $data1, $data2);
  }
  
  // returns true if label data (data1, data2) of label label_id is valid in context of the sequence sequence_id
  public function label_is_valid($label_id, $sequence_id, $data1, $data2 = null)
  {
    $seq_model = $this->load_model('sequence_model');
    
    $result = $this->get_validation_status($label_id, $seq_model->get($sequence_id), $data1, $data2);
    
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

  public function label_exists($id)
  {
    return $this->has_id($id);
  }
  
  public function has_label_data($label_id, $seq_id, $label_type, $data1, $data2 = null)
  {
    $field = $this->__get_data_fields($label_type);
    $this->db->select('id');
    $this->db->where('label_id', $label_id);
    $this->db->where('seq_id', $seq_id);
    
    if(is_array($field)) {
      $field1 = $field[0];
      $field2 = $field[1];
      $this->db->where($field1, $data1);
      $this->db->where($field2, $data2);
    } else {
      $this->db->where($field, $data1);
    }
    
    return $this->count_total('label_sequence');
  }

  public function select_data($label)
  {
    $fields = $this->__get_data_fields($label['type']);

    if(is_array($fields)) {
      return array($label[$fields[0]], $label[$fields[1]]);
    } else {
      return $label[$fields];
    }
  }

  public function get_data($id)
  {
    $this->db->select('id, type, ' . self::$label_data_fields);

    $data = $this->get_id($id, 'label_sequence_info');

    return $this->select_data($data);
  }
  
  // locate sequence id which contains a label with data data1 and data2
  public function get_reverse_data($name_label, $data1, $data2 = null)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get_by_name($name_label);
    if(!$label) {
      return null;
    }
    $type = $label['type'];
    
    $this->db->select('seq_id');
    
    $fields = $this->__get_data_fields($type);
    
    if(is_array($fields)) {
      $this->db->where($fields[0], $data1);
      $this->db->where($fields[1], $data2);
    } else {
      $this->db->where($fields, $data1);
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

    return $this->select_data($all[0]);
  }

  // get label by sequence and label id
  public function get_label_ids($seq_id, $label_id)
  {
    $this->db->select($this->__get_select(), FALSE);
    $this->db->where('seq_id', $seq_id);

    return $this->get_row('label_id', $label_id, 'label_sequence_info');
  }

  private function __get_data_fields($type)
  {
    switch($type) {
    case 'integer':
      return 'int_data';
    case 'text':
    case 'float':
    case 'bool':
    case 'date':
    case 'url':
    case 'ref':
      return $type . '_data';
    case 'obj':
      return array('text_data', 'obj_data');
    case 'position':
      return array('position_start', 'position_length');
    case 'tax':
      return 'taxonomy_data';
    }

    return null;
  }

  private function __compound_oper($oper)
  {
    return $oper == 'and' || $oper == 'or' || $oper == 'not';
  }

  private function __get_search_labels($term, $label_model, &$ret, $only_public)
  {
    if($term != null) {
      $oper = $term['oper'];

      if($this->__compound_oper($oper)) {
        $operands = $term['operands'];
        foreach($operands as $operand) {
          $this->__get_search_labels($operand, $label_model, $ret, $only_public);
        }
      } else {
        $label = $term['label'];
        if(!array_key_exists($label, $ret)) {
          $label_data = $label_model->get_by_name($label);
          if($label_data['public'] || !$only_public) {
            $ret[$label] = $label_data;
          }
        }
      }
    }
  }

  private function _get_search_labels($term, $label_model, $only_public)
  {
    $ret = array();

    $this->__get_search_labels($term, $label_model, $ret, $only_public);

    return $ret;
  }

  private function __translate_sql_oper($oper, $type)
  {
    switch($type) {
    case 'position':
    case 'integer':
    case 'float':
      return sql_oper($oper);
    case 'text':
    case 'url':
    case 'obj':
      switch($oper) {
      case 'eq': return '=';
      case 'contains':
      case 'starts':
      case 'ends':
        return 'LIKE';
      case 'regexp':
        return 'REGEXP';
      default: return '';
      }
    case 'bool': return 'IS';
    case 'tax': return '=';
    case 'ref': return '=';
    case 'date':
      switch($oper) {
        case 'eq': return '=';
        case 'after': return '>';
        case 'before': return '<';
      }
    }
    
    return null;
  }

  private function __translate_sql_value($oper, $value, $type)
  {
    switch($type) {
    case 'position':
    case 'integer':
      if(!isint($value)) {
        return 0;
      }
      
      return $value;
    case 'float':
      if(!is_numeric($value)) {
        return 0.0;
      }
      
      return $value;
    case 'text':
    case 'url':
    case 'obj':
      switch($oper) {
        case 'regexp':
        case 'eq': break;
        case 'contains':
          $value = "%$value%";
          break;
        case 'starts':
          $value = "$value%";
          break;
        case 'ends':
          $value = "%$value";
          break;
        default:
          return "";
      }
      return $this->db->escape($value);
    case 'bool':
      if($value) {
        return 'TRUE';
      } else {
        return 'FALSE';
      }
    case 'tax':
      if(!is_numeric($value)) {
        return 0;
      }
      
      return $value;
    case 'ref':
      $id = $value['id'];
      if(!is_numeric($id)) {
        return 0;
      }
      return $id;
    case 'date':
      $newvalue = convert_html_date_to_sql($value);
      if(!$newvalue) {
        return 'NOW()';
      }
      
      return "DATE('$newvalue')";
    }

    return '';
  }
  
  private function __translate_sql_field($field, $type)
  {
    switch($type) {
      case 'date':
        return "DATE($field)";
      case 'obj':
        return $field[0];
      default:
        return $field;
    }
  }

  private function __get_search_where($term, &$labels, $default = "TRUE")
  {
    if($term == null) {
      return $default;
    }

    $oper = $term['oper'];

    if($oper == 'fail') {
      return 'FALSE';
    } elseif($this->__compound_oper($oper)) {
      $operands = $term['operands'];

      if(empty($operands)) {
        return $default;
      }

      if($oper == 'not') {
        $new_default = 'FALSE';

        $operand = $operands[0];

        $part = $this->__get_search_where($operand, $labels, $new_default);

        return "NOT ($part)";
      }

      $ret = "";
      if($oper == 'and') {
        $new_default = 'TRUE';
        $junction = 'AND';
      } else {
        $new_default = 'FALSE';
        $junction = 'OR';
      }

      for($i = 0; $i < count($operands); ++$i) {
        $part = $this->__get_search_where($operands[$i], $labels, $new_default);

        if($i > 0) {
          $ret .= " $junction ($part)";
        } else {
          $ret .= "($part)";
        }
      }

      return $ret;
    } else {
      $label_name = $term['label'];
      $label = $labels[$label_name];
      $label_type = $label['type'];
      $label_id = $label['id'];
      $oper = $term['oper'];
      
      if(!$label) {
        return 'FALSE'; // label not found
      }
      
      if(!is_numeric($label_id)) {
        return 'FALSE'; // invalid label id
      }

      if(label_special_operator($oper)) {
        if(label_special_purpose($label_name)) {
          switch($label_name) {
            case 'creation_user':
              if($oper == 'exists') {
                return 'creation_user_name IS NOT NULL';
              } else {
                return 'creation_user_name IS NULL';
              }
            case 'update_user':
              if($oper == 'exists') {
                return 'user_name IS NOT NULL';
              } else {
                return 'user_name IS NULL';
              }
            case 'creation_date':
              if($oper == 'exists') {
                return 'creation IS NOT NULL';
              } else {
                return 'creation IS NULL';
              }
            case 'update_date':
              $identifier = $this->db->protect_identifiers('update');
              if($oper == 'exists') {
                return "$identifier IS NOT NULL";
              } else {
                return "$identifier IS NULL";
              }
            default:
              if($oper == 'exists') {
                return 'TRUE';
              } else {
                return 'FALSE';
              }
          }
        }
        
        $sql = "EXISTS (SELECT label_sequence.id FROM label_sequence
          WHERE label_sequence.seq_id = sequence_info_history.id AND label_sequence.label_id = $label_id)";

        if($oper == 'notexists') {
          $sql = "NOT $sql";
        }

        return $sql;
      }

      $value = $term['value'];

      if(!label_special_purpose($label_name)) {
        $fields = $this->__get_data_fields($label_type);

        // handle position fields
        switch($label_type) {
          case 'position':
            $type = $value['type'];
            if($type == 'start') {
              $fields = 'position_start';
            } else {
              $fields = 'position_length';
            }

            $value = $value['num'];
          
            if(!is_numeric($value)) {
              // invalid data
              return 'FALSE';
            }
            break;
        }
      }

      $sql_oper = $this->__translate_sql_oper($oper, $label_type);
      $sql_value = $this->__translate_sql_value($oper, $value, $label_type);

      // handle special purpose labels
      switch($label_name) {
        case 'name':
          return "name $sql_oper $sql_value";
        case 'content':
          return "content $sql_oper $sql_value";
        case 'creation_user':
          return "creation_user_name $sql_oper $sql_value";
        case 'update_user':
          return "user_name $sql_oper $sql_value";
        case 'creation_date':
          return $this->__translate_sql_field('creation', 'date') . " $sql_oper $sql_value";
        case 'update_date':
          return $this->__translate_sql_field($this->db->protect_identifiers('update'), 'date') . " $sql_oper $sql_value";
      }

      $sql_field = $this->__translate_sql_field($fields, $label_type);
      
      return "EXISTS(SELECT label_sequence.id FROM label_sequence WHERE label_sequence.seq_id = sequence_info_history.id
            AND label_sequence.label_id = $label_id AND $sql_field $sql_oper $sql_value)";
    }
  }
  
  // expand search tree cases like taxonomy children and taxonomy and ref seq like operator
  private function __expand_search_tree($term, &$labels)
  {
    if(!$term) {
      return null;
    }
    
    $oper = $term['oper'];
    
    if($this->__compound_oper($oper)) {
      $operands =& $term['operands'];
      $new_operands = array();
      
      foreach($operands as &$operand) {
        $new_operands[] = $this->__expand_search_tree($operand, $labels);
      }
      
      return array('oper' => $oper, 'operands' => $new_operands);
    } else {
      $label_name = $term['label'];
      $label = $labels[$label_name];
      $label_type = $label['type'];
      $oper = $term['oper'];
      
      if(label_special_operator($oper)) {
        return $term;
      }
      
      switch($label_type) {
        case 'ref':
          $val = $term['value'];
          
          if($oper == 'eq') {
            if(!is_numeric($val)) {
              $val = $val['id'];
            }
            if(!is_numeric($val)) {
              return null;
            }
            return array('oper' => $oper, 'label' => $label_name, 'value' => $val);
          } elseif($oper == 'like') {
            $seq_model = $this->load_model('sequence_model');
            $all = $seq_model->get_all(0, 20, array('name' => $value), array(), 'id');
            
            $operands = array();
            
            foreach($all as &$ref) {
              $operands[] = array('label' => $label_name, 'oper' => 'eq', 'value' => $ref['id']);
            }
            
            if(empty($operands)) {
              return array('oper' => 'fail');
            }
            
            return array('oper' => 'oper', 'operands' => &$operands);
          }
          break;
        case 'tax':
          $val = $term['value'];
          $tax_model = $this->load_model('taxonomy_model');
          
          if($oper == 'eq') {
            if(!is_numeric($val)) {
              $val = $val['id'];
            }
            if(!is_numeric($val)) {
              return null;
            }
            $descendants = $tax_model->get_taxonomy_descendants($val, null, 'id');
          } else if($oper == 'like') {
            $all = $tax_model->search($val, null, null, 0, 20, array(), 'id');
            $descendants = array();
            
            // get all descendants
            foreach($all as &$tax) {
              $id = $tax['id'];
              $this_descendants = $tax_model->get_taxonomy_descendants($id, null, 'id');
              $descendants = array_merge($descendants, $this_descendants);
            }
          }
          
          $operands = array();
          
          foreach($descendants as $descendant) {
            $operands[] = array('oper' => 'eq', 'value' => $descendant['id'], 'label' => $label_name);
          }
          
          if(empty($operands)) {
            return array('oper' => 'fail');
          }
          
          return array('oper' => 'or', 'operands' => &$operands);
        default:
          return $term;
      }
    }
  }

  private function __get_search_sql($search, $only_public = false)
  {
    $label_model = $this->load_model('label_model');
    $labels = $this->_get_search_labels($search, $label_model, $only_public);
    $new_search = $this->__expand_search_tree($search, $labels);
    $sql_part = $this->__get_search_where($new_search, $labels);
    if($only_public) {
      return $sql_part . ' AND ' . self::$public_sequence_where;
    } else {
      return $sql_part;
    }
  }

  public function get_search($search, $start = null, $size = null, $ordering = array(), $transform = null, $only_public = false)
  {
    $sql_where = $this->__get_search_sql($search, $only_public);
    $sql_limit = sql_limit($start, $size);
    $sql_order = $this->get_order_sql($ordering, 'name', 'asc');
    $select_sql = "DISTINCT id, user_name, update_user_id, `update`, name";
    
    if($transform) {
      if($only_public) {
        $public_where = 'WHERE ' . self::$public_sequence_where;
      } else {
        $public_where = '';
      }
      
      $sql = "SELECT $select_sql
              FROM (SELECT id AS orig_id FROM sequence_info_history WHERE $sql_where) all_seqs
                  NATURAL JOIN
                   (SELECT seq_id AS orig_id, ref_data AS id FROM label_sequence WHERE label_id = $transform
                                                                    AND ref_data IS NOT NULL) label_seqs
                  NATURAL JOIN
                 (SELECT * FROM sequence_info_history $public_where) every_seqs
              $sql_order $sql_limit";
    } else {
      $sql = "SELECT $select_sql
          FROM sequence_info_history
          WHERE $sql_where $sql_order $sql_limit";
    }

    return $this->rows_sql($sql);
  }

  // get total number of sequences with this search tree
  public function get_search_total($search, $transform = null, $only_public = false)
  {
    $sql_where = $this->__get_search_sql($search, $only_public);
    
    if($transform) {
      if($only_public) {
        $public_where = 'WHERE EXISTS(SELECT * FROM label_sequence_info WHERE label_sequence_info.seq_id = label_seqs.ref_data AND label_sequence_info.name = \'perm_public\' AND label_sequence_info.bool_data IS TRUE)';
      } else {
        $public_where = '';
      }
      $sql = "SELECT count(DISTINCT ref_data) AS total
              FROM (SELECT id FROM sequence_info_history WHERE $sql_where) all_seqs
                      NATURAL JOIN (SELECT seq_id AS id, ref_data FROM label_sequence WHERE label_id = $transform AND ref_data IS NOT NULL) label_seqs
              $public_where";
    } else {
      $sql = "SELECT count(id) AS total
              FROM sequence_info_history
              WHERE $sql_where";
    }

    return $this->total_sql($sql);
  }

  // get all labels from this sequence
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