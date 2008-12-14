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

  function add($name, $type, $autoadd, $comment)
  {
    $data = array(
      'name' => $name,
      'type' => $type,
      'autoadd' => $autoadd,
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
    $this->delete_id($id);
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

  function edit_comment($id, $comment)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'comment', $comment);

    $this->db->trans_complete();
  }
}
