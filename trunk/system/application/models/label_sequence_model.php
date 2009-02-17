<?php

class Label_sequence_model extends BioModel
{
  function Label_sequence_model() {
    parent::BioModel('label_sequence');
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

  function __get_data_fields($type)
  {
    switch($type) {
    case "integer":
      return "int_data";
    case "text":
      return "text_data";
    case "obj":
      return "obj_data";
    case "position":
      return array("position_a_data", "position_b_data");
    case "ref":
      return "ref_data";
    case "tax":
      return "tax_data";
    case "url":
      return "url_data";
    }

    return "";
  }
}
