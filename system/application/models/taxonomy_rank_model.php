<?php

class Taxonomy_rank_model extends Model
{
  public static $table = 'taxonomy_rank';

  function Taxonomy_rank_model()
  {
    parent::Model();
  }

  function get_ranks()
  {
    return $this->db->get(self::$table)->result_array();
  }

  function edit($id, $new_name)
  {
    if($this->has($new_name)) {
      return false;
    }

    $this->db->where('id', $id);
    $data = array(
      'name' => $new_name,
    );

    $this->db->update(self::$table, $data);

    return true;
  }

  function get($id) {
    $query = $this->db->get_where(self::$table, array('id' => $id));

    return $query->row_array();
  }

  function get_name($id)
  {
    $this->db->select('name');
    $query = $this->db->get_where(self::$table, array('id' => $id));

    $data = $query->row_array();

    return $data['name'];
  }

  function has_id($id)
  {
    $query = $this->db->get_where(self::$table, array('id' => $id));

    return $query->num_rows() == 1;
  }

  function has($name)
  {
    $query = $this->db->get_where(self::$table, array('name' => $name));

    return $query->num_rows() == 1;
  }

  function add($name)
  {
    $data = array('name' => $name);
    $this->db->insert(self::$table, $data);

    return $this->db->insert_id();
  }

  function add_array($arr)
  {
    foreach($arr as $name) {
      if(!$this->has($name)) {
        $this->add($name);
      }
    }
  }

  function delete($id)
  {
    $this->db->delete(self::$table, array('id' => $id));
  }
}

