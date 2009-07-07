<?php

class Label_sequence_model extends BioModel
{
  private static $label_data_fields = "int_data, text_data, obj_data, ref_data, position_a_data, position_b_data, taxonomy_data, url_data, bool_data, taxonomy_name, sequence_name";

  private static $label_basic_fields = "label_id, id, seq_id, subname, history_id, type, name, default, must_exist, auto_on_creation, auto_on_modification, deletable, editable, multiple";

  function Label_sequence_model() {
    parent::BioModel('label_sequence');
  }

  function __get_select()
  {
    return self::$label_basic_fields . ", " . self::$label_data_fields;
  }

  function get_label_id($seq_id, $label_id)
  {
    $this->db->where('seq_id', $seq_id);
    return $this->get_id_by_field('label_id', $label_id);
  }

  function get($id)
  {
    $this->db->select($this->__get_select() . ", code");
    return $this->get_id($id, 'label_sequence_info');
  }

  function get_sequence($id)
  {
    $this->db->select($this->__get_select() . ", update_user_id, update, user_name");
    $this->db->where('seq_id', $id);
    return $this->get_all('label_sequence_info');
  }

  function add($seq, $label, $type, $subname, $data1 = null, $data2 = null)
  {
    $data = array(
      'seq_id' => $seq,
      'label_id' => $label,
      'subname' => $subname,
    );

    $fields = $this->__get_data_fields($type);

    if(is_array($fields)) {
      $data[$fields[0]] = $data1;
      $data[$fields[1]] = $data2;
    } else {
      $data[$fields] = $data1;
    }

    return $this->insert_data_with_history($data);
  }

  function add_generated($seq_id, $label)
  {
    $data1 = null;
    $data2 = null;

    $res = $this->generate_label_value($seq_id, $label['code']);

    if(is_array($res)) {
      $data1 = $res[0];
      $data2 = $res[1];
    } else {
      $data1 = $res;
    }

    $this->add($seq_id, $label['id'], $label['type'], null, $data1, $data2);
  }

  function add_generated_label($seq_id, $label_id, $type)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($label['type'] == $type && $label['code']) {
      $this->add_generated($seq_id, $label);
      return true;
    } else {
      return false;
    }
  }

  function edit($id, $type, $data1 = null, $data2 = null)
  {
    $fields = $this->__get_data_fields($type);

    $data = array();

    if(is_array($fields)) {
      $data[$fields[0]] = $data1;
      $data[$fields[1]] = $data2;
    } else {
      $data[$fields] = $data1;
    }

    return $this->edit_data_with_history($id, $data);
  }

  function add_initial_labels($id)
  {
    $label_model = $this->load_model('label_model');
    $labels = $label_model->get_to_add();

    foreach($labels as $label)
    {
      $this->add_initial_label($id, $label);
    }
  }

  function generate_label_value($id, $code)
  {
    $seq_model = $this->load_model('sequence_model');

    $seq = $seq_model->get_id($id);

    $name = $seq['name'];
    $content = $seq['content'];

    return eval($code);
  }

  function add_initial_label($id, $label)
  {
    $data1 = null;
    $data2 = null;

    if($label['default'] || $label['auto_on_creation']) {
      $res = $this->generate_label_value($id, $label['code']);

      if(is_array($res)) {
        $data1 = $res[0];
        $data2 = $res[1];
      } else {
        $data1 = $res;
      }
    }

    $this->add($id, $label['id'], $label['type'], null, $data1, $data2);
  }

  function add_auto_label($seq, $label)
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

    $this->add($seq, $label['id'], $label['type'], null, $data1, $data2);
  }

  function add_auto_label_id($seq, $label_id)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    return $this->add_auto_label($seq, $label);
  }

  function edit_auto_label($id)
  {
    $label = $this->get($id);

    return $this->regenerate_label($label['seq_id'], $label);
  }

  function __is_text($label)
  {
    return $label['type'] == 'text';
  }

  function add_text_label($seq_id, $label_id, $text)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_text($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'text', null, $text);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_text_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'text');
  }

  function edit_text_label($id, $text)
  {
    $label = $this->get($id);

    if($this->__is_text($label) && $label['editable']) {
      return $this->edit($id, 'text', $text);
    } else {
      return false;
    }
  }

  function edit_generated_text_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_integer($label)
  {
    return $label['type'] == 'integer';
  }

  function add_integer_label($seq_id, $label_id, $int)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_integer($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'integer', null, $int);
      return true;
    } else {
      return false;
    }
  }

  function edit_integer_label($id, $int)
  {
    $label = $this->get($id);

    if($this->__is_integer($label) && $label['editable']) {
      return $this->edit($id, 'integer', $int);
    } else {
      return false;
    }
  }

  function edit_generated_integer_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function add_generated_integer_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'integer');
  }

  function __is_bool($label)
  {
    return $label['type'] == 'bool';
  }

  function add_bool_label($seq_id, $label_id, $bool)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_bool($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'bool', null, $bool);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_bool_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'bool');
  }

  function edit_bool_label($id, $bool)
  {
    $label = $this->get($id);

    if($this->__is_bool($label) && $label['editable']) {
      return $this->edit($id, 'bool', $bool);
    } else {
      return false;
    }
  }

  function edit_generated_bool_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_url($label)
  {
    return $label['type'] == 'url';
  }

  function add_url_label($seq_id, $label_id, $url)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_url($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'url', null, $url);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_url_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'url');
  }

  function edit_url_label($id, $url)
  {
    $label = $this->get($id);

    if($this->__is_url($label) && $label['editable']) {
      return $this->edit($id, 'url', $url);
    } else {
      return false;
    }
  }

  function edit_generated_url_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_obj($label)
  {
    return $label['type'] == 'obj';
  }

  function add_obj_label($seq_id, $label_id, $filename, $data)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_obj($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'obj', null, $filename, $data);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_obj_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'obj');
  }

  function edit_obj_label($id, $filename, $data)
  {
    $label = $this->get($id);

    if($this->__is_obj($label) && $label['editable']) {
      return $this->edit($id, 'obj', $filename, $data);
    } else {
      return false;
    }
  }

  function edit_generated_obj_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_ref($label)
  {
    return $label['type'] == 'ref';
  }

  function add_ref_label($seq_id, $label_id, $ref)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_ref($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'ref', null, $ref);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_ref_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'ref');
  }

  function edit_ref_label($id, $ref)
  {
    $label = $this->get($id);

    if($this->__is_ref($label) && $label['editable']) {
      return $this->edit($id, 'ref', $ref);
    } else {
      return false;
    }
  }

  function edit_generated_ref_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_tax($label)
  {
    return $label['type'] == 'tax';
  }

  function add_tax_label($seq_id, $label_id, $tax)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_tax($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'tax', null, $tax);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_tax_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'tax');
  }

  function edit_tax_label($id, $tax)
  {
    $label = $this->get($id);

    if($this->__is_tax($label) && $label['editable']) {
      return $this->edit($id, 'tax', $tax);
    } else {
      return false;
    }
  }

  function edit_generated_tax_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function __is_position($label)
  {
    return $label['type'] == 'position';
  }

  function add_position_label($seq_id, $label_id, $start, $length)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    if($this->__is_position($label) && $label['editable']) {
      $this->add($seq_id, $label_id, 'position', null, $start, $length);
      return true;
    } else {
      return false;
    }
  }

  function add_generated_position_label($seq_id, $label_id)
  {
    return $this->add_generated_label($seq_id, $label_id, 'position');
  }

  function edit_position_label($id, $start, $length)
  {
    $label = $this->get($id);

    if($this->__is_position($label) && $label['editable']) {
      return $this->edit($id, 'position', $start, $length);
    } else {
      return false;
    }
  }

  function edit_generated_position_label($id)
  {
    return $this->edit_auto_label($id);
  }

  function get_labels_to_auto_modify($seq)
  {
    $this->db->where('auto_on_modification', true);
    $this->db->where('seq_id', $seq);

    return $this->get_all('label_sequence_info');
  }

  function regenerate_label($seq, $label)
  {
    $id = $label['id'];
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

  function regenerate_labels($seq)
  {
    $labels = $this->get_labels_to_auto_modify($seq);

    $this->db->trans_start();

    foreach($labels as $label) {
      $this->regenerate_label($seq, $label);
    }

    $this->db->trans_complete();
  }

  function total_label($id)
  {
    $this->db->where('label_id', $id);

    return $this->count_total();
  }

  function edit_subname($id, $subname)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'subname', $subname);

    $this->db->trans_complete();
  }

  function delete($id)
  {
    $info = $this->get_id($id, 'label_sequence_info');

    if(!$info['deletable']) {
      return false;
    }

    $this->delete_id($id);

    return true;
  }

  function get_obligatory($id)
  {
    $this->db->distinct();
    $this->db->select('label_id');
    $this->db->where('seq_id', $id);
    $this->db->where('must_exist', TRUE);

    $labels = $this->get_all('label_sequence_info');

    $ret = array();

    foreach($labels as $label) {
      $ret[] = intval($label['label_id']);
    }

    return $ret;
  }

  function get_missing_obligatory_ids($id)
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

  function get_missing_obligatory($id)
  {
    $ids = $this->get_missing_obligatory_ids($id);
    $label_model = $this->load_model('label_model');

    $ret = array();
    foreach($ids as $id) {
      $ret[] = $label_model->get_simple($id);
    }

    return $ret;
  }

  function has_missing($id)
  {
    return count($this->get_missing_obligatory($id)) > 0;
  }

  function get_addable_labels($id)
  {
    $sql = "SELECT id, name, type, must_exist, auto_on_creation,
          auto_on_modification, deletable, editable, multiple
      FROM label
      WHERE multiple IS TRUE OR
            id NOT IN (SELECT DISTINCT label_id AS id
                      FROM label_sequence
                      WHERE seq_id = $id)";

    $query = $this->db->query($sql);

    if(!$query) {
      return null;
    }

    $data = $query->result_array();

    return $data;
  }

  function __get_validation_status($label, $sequence, $valid_code, $data1, $data2)
  {
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

  function get_validation_status($label_id, $sequence, $data1, $data2)
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);

    $valid_code = $label['valid_code'];

    if(!$valid_code) {
      return 'no validation';
    }

    return $this->__get_validation_status($label, $sequence, $valid_code, $data1, $data2);
  }

  function get_validation_label($sequence, $label)
  {
    $ret = $label;

    $label_id = $label['label_id'];

    $field1 = null;
    $field2 = null;

    $fields = $this->__get_data_fields($label['type']);
    if(is_array($fields)) {
      $field1 = $fields[0];
      $field2 = $fields[1];
    } else {
      $field1 = $fields;
    }

    $data1 = $label[$field1];
    $data2 = null;
    if($field2) {
      $data2 = $label[$field2];
    }

    $ret['status'] =
      $this->get_validation_status($label_id, $sequence, $data1, $data2);

    return $ret;
  }

  function get_validation_labels($id)
  {
    $sequence_model = $this->load_model('sequence_model');
    $sequence = $sequence_model->get($id);

    $labels = $this->get_sequence($id);

    $ret = array();

    foreach($labels as $label) {
      $ret[] = $this->get_validation_label($sequence, $label);
    }

    return $ret;
  }

  function __bad_multiple_sql($id)
  {
      return "FROM label_sequence_info AS lsi
      WHERE multiple IS FALSE AND
            label_id IN (SELECT label_id
                         FROM label_sequence AS ls
                         WHERE ls.id <> lsi.id AND
                           ls.label_id = lsi.label_id
                           AND ls.seq_id = $id)
           AND seq_id = $id";
  }

  function get_bad_multiple($id)
  {
  $sql = "SELECT id, label_id, seq_id, name, type, " . self::$label_data_fields . " " .
      $this->__bad_multiple_sql($id);

    return $this->rows_sql($sql);
  }

  function has_bad_multiple($id)
  {
    $sql = "SELECT count(id) AS total  " .
      $this->__bad_multiple_sql($id);

    return $this->total_sql($sql) > 0;
  }

  function label_used_up($seq, $label)
  {
    $this->db
      ->where('seq_id', $seq)
      ->where('label_id', $label)
      ->where('multiple IS FALSE');

    return $this->count_total('label_sequence_info') > 0;
  }

  function get_label_by_seq_name($seq_id, $label_name)
  {
    $this->db->select(self::$label_data_fields . ' id, seq_id, label_id, type');
    $this->db->where('seq_id', $seq_id);

    return $this->get_row('name', $label_name, 'label_sequence_info');
  }

  function count_taxonomies($tax)
  {
    $this->db->select('id');
    $this->db->where('taxonomy_data', $tax);
    return $this->count_total();
  }

  function count_sequences_for_label($label)
  {
    $this->db->select('DISTINCT id');
    $this->db->where('label_id', $label);
    return $this->count_total();
  }

  function label_exists($id)
  {
    return $this->has_id($id);
  }

  function select_data($label)
  {
    $fields = $this->__get_data_fields($label['type']);

    if(is_array($fields)) {
      return array($label[$fields[0]], $label[$fields[1]]);
    } else {
      return $label[$fields];
    }
  }

  function get_data($id)
  {
    $this->db->select('id, type, ' . self::$label_data_fields);

    $data = $this->get_id($id, 'label_sequence_info');

    return $this->select_data($data);
  }

  function get_label($seq_id, $label_name)
  {
    $this->db->select('id, type, ' . self::$label_data_fields);
    $this->db->where('name', $label_name);
    $this->db->where('seq_id', $seq_id);
    $all = $this->get_all('label_sequence_info');

    if(!$all || count($all) == 0) {
      return null;
    }

    return $this->select_data($all[0]);
  }

  function __get_data_fields($type)
  {
    switch($type) {
    case 'integer':
      return 'int_data';
    case 'text':
      return 'text_data';
    case 'obj':
      return array('text_data', 'obj_data');
    case 'position':
      return array('position_a_data', 'position_b_data');
    case 'ref':
      return 'ref_data';
    case 'tax':
      return 'taxonomy_data';
    case 'url':
      return 'url_data';
    case 'bool':
      return 'bool_data';
    }

    return "";
  }

  function __get_search_labels($term, $label_model, &$ret)
  {
    if($term != null) {
      $oper = $term['oper'];

      if($oper == 'and' || $oper == 'or') {
        $operands = $term['operands'];
        foreach($operands as $operand) {
          $this->__get_search_labels($operand, $label_model, $ret);
        }
      } else {
        $label = $term['label'];
        if(!array_key_exists($label, $ret)) {
          $ret[$label] = $label_model->get_by_name($label);
        }
      }
    }
  }

  function _get_search_labels($term, $label_model)
  {
    $ret = array();

    $this->__get_search_labels($term, $label_model, $ret);

    return $ret;
  }

  function __translate_sql_oper($oper, $type)
  {
    switch($type) {
    case 'integer':
      switch($oper) {
      case 'eq': return '=';
      case 'gt': return '>';
      case 'lt': return '<';
      case 'ge': return '>=';
      case 'le': return '<=';
      default: return '';
      }
    case 'text':
    case 'url':
      switch($oper) {
      case 'eq': return '=';
      case 'contains':
      case 'starts':
      case 'ends':
        return 'LIKE';
      default: return '';
      }
    case 'bool': return 'IS';
    case 'tax': return '=';
    case 'ref': return '=';
    }
  }

  function __translate_sql_value($oper, $value, $type)
  {
    switch($type) {
    case 'integer':
      return $value;
    case 'text':
    case 'url':
      switch($oper) {
        case 'eq': return "\"$value\"";
        case 'contains': return "\"%$value%\"";
        case 'starts': return "\"$value%\"";
        case 'ends': return "\"%$value\"";
      }
    case 'bool':
      if($value) {
        return 'TRUE';
      } else {
        return 'FALSE';
      }
    case 'tax':
      return $value['id'];
    case 'ref':
      return $value['id'];
    }

    return '';
  }

  function __get_search_where($term, &$labels, $default = "TRUE")
  {
    if($term == null) {
      return $default;
    }

    $oper = $term['oper'];

    if($oper == 'and' || $oper == 'or') {
      $operands = $term['operands'];

      if(count($operands) == 0) {
        return $default;
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
      $fields = $this->__get_data_fields($label_type);
      $oper = $term['oper'];
      $value = $term['value'];
      $sql_oper = $this->__translate_sql_oper($oper, $label_type);
      $sql_value = $this->__translate_sql_value($oper, $value, $label_type);

      $sql = "name = \"$label_name\" AND $fields IS NOT NULL AND $fields $sql_oper $sql_value";

      return $sql;
    }
  }

  function __get_search_sql($search)
  {
    $label_model = $this->load_model('label_model');
    $labels = $this->_get_search_labels($search, $label_model);
    $sql_part = $this->__get_search_where($search, $labels);
    return $sql_part;
  }

  function get_search($search, $start, $size, $ordering = array())
  {
    $sql_where = $this->__get_search_sql($search);
    $sql_limit = sql_limit($start, $size);
    $sql_order = $this->get_order_sql($ordering, 'name', 'asc');
    $sql = "SELECT DISTINCT id, user_name, update_user_id, `update`, name
      FROM sequence_info_history INNER JOIN (SELECT DISTINCT seq_id FROM label_sequence_info WHERE $sql_where $sql_limit) AS C ON (sequence_info_history.id = C.seq_id) $sql_order";

    return $this->rows_sql($sql);
  }

  function get_search_total($search)
  {
    $sql_where = $this->__get_search_sql($search);
    $sql = "SELECT count(DISTINCT seq_id) AS total
            FROM label_sequence_info
            WHERE $sql_where";
    return $this->total_sql($sql);
  }
}
