<?php

class Label_model extends BioModel
{
  function Label_model() {
    parent::BioModel('label');
  }

  function __process_label(&$label)
  {
    return $label;
  }

  function __process_labels(&$labels)
  {
    $ret = array();

    foreach($labels as $label) {
      $ret[] = $this->__process_label($label);
    }

    return $ret;
  }

  function get($id)
  {
    return $this->__process_label($this->get_id($id));
  }

  function get_all()
  {
    $this->db->order_by('name');
    return $this->__process_labels(parent::get_all());
  }

  function count_names($name)
  {
    $this->db->where('name', $name);

    return $this->count_total();
  }

  function add($name, $type, $autoadd, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple, $code, $comment)
  {
    $data = array(
      'name' => $name,
      'type' => $type,
      'autoadd' => $autoadd,
      'must_exist' => $mustexist,
      'auto_on_creation' => $auto_on_creation,
      'auto_on_modification' => $auto_on_modification,
      'deletable' => $deletable,
      'editable' => $editable,
      'multiple' => $multiple,
      'code' => $code,
      'comment' => $comment,
    );

    return $this->insert_data_with_history($data);
  }

  function edit($id, $name, $type, $autoadd, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple, $code, $comment)
  {
    $data = array(
      'name' => $name,
      'type' => $type,
      'autoadd' => $autoadd,
      'must_exist' => $mustexist,
      'auto_on_creation' => $auto_on_creation,
      'auto_on_modification' => $auto_on_modification,
      'deletable' => $deletable,
      'editable' => $editable,
      'multiple' => $multiple,
      'code' => $code,
      'comment' => $comment,
    );

    return $this->edit_data_with_history($id, $data);
  }

  function has($name)
  {
    return $this->has_field('name', $name);
  }

  function delete($id)
  {
    $deletable = $this->get_field($id, 'deletable');
    $default = $this->get_field($id, 'default');

    if($default) {
      return false;
    } else {
      $this->delete_id($id);
      return true;
    }
  }

  function is_default($id)
  {
    return $this->get_field($id, 'default');
  }

  function is_deletable($id)
  {
    return $this->get_field($id, 'deletable');
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function get_to_add()
  {
    $this->db->where('autoadd', TRUE);

    return parent::get_all();
  }
}
