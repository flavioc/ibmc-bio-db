<?php

class Configuration_model extends BioModel
{
  private static $default_paging_size = 30;

  function Configuration_model()
  {
    parent::BioModel('configuration');
  }

  public function get_key($key, $user = null)
  {
    if($user == null)
      $user = $this->user_id;
      
    if($this->user_id == null)
      return null;

    $this->db->select('value');
    $this->db->where('user_id', $this->user_id);
    $this->db->where('key', $key);

    $query = $this->db->get($this->table);

    if($query->num_rows() != 1)
      return null;

    $data = $query->row_array();

    return unserialize($data['value']);
  }

  public function has_key($key, $user = null)
  {
    if($user == null)
      $user = $this->user_id;
      
    if($this->user_id == null)
      return false;

    $this->db->select('value');
    $this->db->where('user_id', $this->user_id);
    $this->db->where('key', $key);

    return $this->has_something();
  }

  public function set_key($key, $value, $user = null)
  {
    if($user == null)
      $user = $this->user_id;
      
    if($this->user_id == null)
      return false;

    $serialized_value = serialize($value);

    if($this->has_key($key)) {
      // update existing key
      $this->db->where('user_id', $this->user_id);
      $this->db->where('key', $key);

      $this->db->update($this->table,
        array('value' => $serialized_value));
      
    } else {
      // insert new key
      $data = array(
        'user_id' => intval($this->user_id),
        'key' => $key,
        'value' => $serialized_value,
      );

      $this->db->insert($this->table, $data);
    }

    return true;
  }

  public function set_paging_size($size)
  {
    $this->set_key('paging-size', $size);

    return $size;
  }

  public function get_paging_size()
  {
    $current = $this->get_key('paging-size');

    if($current == null) {
      $this->set_key('paging-size', self::$default_paging_size);
      return self::$default_paging_size;
    }

    return $current;
  }
}