<?php

class Taxonomy_name_model extends Model
{
  public static $table = 'taxonomy_name';

  function Taxonomy_name_model()
  {
    parent::Model();
  }

  function get($id)
  {
    $query = $this->db->get_where(self::$table, array('id' => $id));

    if(!$query || $query->num_rows() != 1) {
      return null;
    }

    return $query->row_array();
  }

  function get_tax($tax_id)
  {
    $query = $this->db->get_where('taxonomy_name_and_type', array('tax_id' => $tax_id));

    $data = $query->result_array();

    return $data;
  }

  function edit_type($id, $type_id)
  {
    $this->db->where('id', $id);
    $data = array(
      'type_id' => $type_id,
    );

    $this->db->update(self::$table, $data);
  }

  function edit_name($id, $name)
  {
    $this->db->where('id', $id);
    $data = array(
      'name' => $name,
    );

    $this->db->update(self::$table, $data);
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

  function add($tax, $name, $type)
  {
    $data = array(
      'tax_id' => $tax,
      'name' => $name,
      'type_id' => $type,
    );

    $this->db->insert(self::$table, $data);

    return $this->db->insert_id();
  }

  function delete($id)
  {
    $this->db->delete(self::$table, array('id' => $id));
  }
}
