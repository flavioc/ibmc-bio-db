<?php

class Label_model extends BioModel
{
  function Label_model() {
    parent::BioModel('label');
  }

  function __select()
  {
    $this->db->select('label_id AS id, type, name, autoadd, default, must_exist, auto_on_creation, auto_on_modification, code, deletable, editable, multiple, update_user_id, update, user_name, comment');
  }

  function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'label_info_history', 'label_id');
  }

  function get_all()
  {
    $this->db->order_by('name');
    $this->__select();
    return parent::get_all('label_info_history');
  }

  function count_names($name)
  {
    $this->db->where('name', $name);

    return $this->count_total();
  }

  function add($name, $type, $autoadd, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple,
    $default, $code, $comment)
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
      'default' => $default,
      'code' => $code,
      'comment' => $comment,
    );

    return $this->insert_data_with_history($data);
  }

  function edit($id, $name, $type, $autoadd, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple,
    $default, $code, $comment)
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
      'default' => $default,
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

  function get_obligatory()
  {
    $this->db->select('id');
    $this->db->where('must_exist', TRUE);

    $all = parent::get_all();

    $ret = array();
    foreach($all as $label) {
      $ret[] = intval($label['id']);
    }

    return $ret;
  }
}
