<?php

class Taxonomy_name_type_model extends Model
{
  public static $table = 'taxonomy_name_type';

  function Taxonomy_name_type_model()
  {
    parent::Model();
  }

  function get_all()
  {
    return $this->db->get(self::$table)->result_array();
  }

  function get($id)
  {
    $query = $this->db->get_where(self::$table, array('id' => $id));

    if(!$query || $query->num_rows() != 1) {
      return null;
    }

    return $query->row_array();
  }

  function get_name($id)
  {
    $this->db->select('name');
    $query = $this->db->get_where(self::$table, array('id' => $id));

    if(!$query || $query->num_rows() != 1) {
      return null;
    }

    $data = $query->row_array();

    return $data['name'];
  }
}
