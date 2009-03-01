<?php

class History_model extends Model
{
  private static $table = 'history';

  function History_model()
  {
    parent::Model();
  }

  function has_id($id)
  {
    $query = $this->db->get_where(self::$table, array('id' => $id));

    return $query->num_rows() == 1;
  }

  function update($id)
  {
    if($this->user_id == null) {
      return null;
    }

    $this->db->where('id', $id);
    $data = array(
      'update_user_id' => $this->user_id,
    );

    $this->db->update(self::$table, $data);

    return true;
  }

  function add()
  {
    if($this->user_id == null) {
      return null;
    }

    $data = array('update_user_id' => $this->user_id);
    $this->db->insert(self::$table, $data);

    return $this->db->insert_id();
  }

  function get($id)
  {
    $query = $this->db->get_where('history_info', array('id' => $id));

    if($query->num_rows() != 1) {
      return null;
    }

    return $query->row_array();
  }
}
