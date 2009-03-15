<?php

class Label_sequence_model extends BioModel
{
  function Label_sequence_model() {
    parent::BioModel('label_sequence');
  }

  function process_label_sequence(&$label)
  {
    if($label['type'] == 'tax') {
      $taxonomy = $this->load_model('taxonomy_model');
      $label['taxonomy_name'] = $taxonomy->get_name($label['taxonomy_data']);
    } else if($label['type'] == 'ref') {
      $sequence = $this->load_model('sequence_model');
      $label['sequence_name'] = $sequence->get_name($label['ref_data']);
    }

    return $label;
  }

  function process_label_sequences(&$labels)
  {
    $ret = array();
    foreach($labels as $label) {
      $ret[] = $this->process_label_sequence(&$label);
    }

    return $ret;
  }

  function get_sequence($id)
  {
    $this->db->where('seq_id', $id);
    return $this->process_label_sequences(
      $this->get_all('label_sequence_info'));
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
    $accession = $seq['accession'];
    $type = $seq['type'];
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

    $this->edit($id, $type, $data1, $data2);
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
      $ret[] = $label_model->get($id);
    }

    return $ret;
  }

  function get_addable_labels($id)
  {
    $sql = "SELECT *
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

  function label_used_up($seq, $label)
  {
    $this->db
      ->where('seq_id', $seq)
      ->where('label_id', $label)
      ->where('multiple IS FALSE');

    return $this->count_total('label_sequence_info') > 0;
  }

  function __get_data_fields($type)
  {
    switch($type) {
    case "integer":
      return "int_data";
    case "text":
      return "text_data";
    case "obj":
      return array("text_data", "obj_data");
    case "position":
      return array("position_a_data", "position_b_data");
    case "ref":
      return "ref_data";
    case "tax":
      return "taxonomy_data";
    case "url":
      return "url_data";
    case "bool":
      return "bool_data";
    }

    return "";
  }
}
