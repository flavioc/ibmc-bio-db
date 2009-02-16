<?php

class Label_model extends BioModel
{
  function Label_model() {
    parent::BioModel('label');
  }

  function get($id)
  {
    return $this->get_id($id);
  }

  function get_all()
  {
    $this->db->order_by('name');
    return parent::get_all();
  }

  function add($name, $type, $autoadd, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable, $code, $comment)
  {
    $data = array(
      'name' => $name,
      'type' => $type,
      'autoadd' => $autoadd,
      'must_exist' => $mustexist,
      'auto_on_creation' => $auto_on_creation,
      'auto_on_modification' => $auto_on_modification,
      'deletable' => $deletable,
      'code' => $code,
      'comment' => $comment,
    );

    return $this->insert_data_with_history($data);
  }

  function has($name)
  {
    return $this->has_field('name', $name);
  }

  function delete($id)
  {
    $deletable = $this->get_field($id, 'deletable');
    $default = $this->get_field($id, 'default');

    if(!$deletable || $default) {
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

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function edit_name($id, $name)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
  }

  function edit_type($id, $type)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'type', $type);

    $this->db->trans_complete();
  }

  function edit_autoadd($id, $autoadd)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'autoadd', $autoadd);

    $this->db->trans_complete();
  }

  function edit_mustexist($id, $mustexist)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'must_exist', $mustexist);

    $this->db->trans_complete();
  }

  function edit_auto_on_creation($id, $auto_on_creation)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'auto_on_creation', $auto_on_creation);

    $this->db->trans_complete();
  }

  function edit_auto_on_modification($id, $auto_on_modification)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'auto_on_modification', $auto_on_modification);

    $this->db->trans_complete();
  }

  function edit_deletable($id, $deletable)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'deletable', $deletable);

    $this->db->trans_complete();
  }

  function edit_comment($id, $comment)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'comment', $comment);

    $this->db->trans_complete();
  }

  function edit_code($id, $code)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'code', $code);

    $this->db->trans_complete();
  }
}
