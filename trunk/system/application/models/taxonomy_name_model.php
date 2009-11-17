<?php

class Taxonomy_name_model extends BioModel
{
  function Taxonomy_name_model()
  {
    parent::BioModel('taxonomy_name');
  }

  public function get($id)
  {
    return $this->get_id($id);
  }

  public function get_name_and_type($id)
  {
    return $this->get_id($id, 'taxonomy_name_info');
  }

  public function get_tax($tax_id)
  {
    return $this->get_all_by_field('tax_id', $tax_id, 'taxonomy_name_info');
  }

  public function get_tax_id($id)
  {
    return $this->get_field($id, 'tax_id');
  }

  public function edit_type($id, $type_id)
  {
    $type_model = $this->load_model('taxonomy_name_type_model');
    if(!$type_model->has_id($type_id)) {
      return false;
    }
    
    $tax = $this->get_tax_id($id);

    $this->db->trans_start();

    $ret = $this->edit_field($id, 'type_id', $type_id);
    
    if($ret) {
      $ret = $this->update_history($tax, 'taxonomy');
    }

    $this->db->trans_complete();
  
    return $ret;
  }

  public function edit_name($id, $name)
  {
    $tax = $this->get_tax_id($id);

    $this->db->trans_start();

    $ret = $this->edit_field($id, 'name', trim($name));
    if($ret) {
      $ret = $this->update_history($tax, 'taxonomy');
    }

    $this->db->trans_complete();
    
    return $ret;
  }

  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  public function add($tax, $name, $type)
  {
    $tax_model = $this->load_model('taxonomy_model');
    
    if(!$tax_model->has_id($tax)) {
      return false;
    }
    
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 512) {
      return false;
    }
    
    $data = array(
      'tax_id' => $tax,
      'name' => $name,
      'type_id' => $type,
    );

    $this->db->trans_start();

    $ret = $this->insert_data($data);
    if($ret) {
      $this->update_history($tax, 'taxonomy');
    }

    $this->db->trans_complete();

    return $ret;
  }

  public function delete($id)
  {
    return $this->delete_id($id);
  }

  public function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }

  public function get_id_by_name_and_tax($tax, $name)
  {
    $this->db->select('id');
    $this->db->where('tax_id', intval($tax));
    $this->db->where('name', $name);

    $data = $this->db->get($this->table)->row_array();

    if($data == null) {
      return null;
    }

    return $data['id'];
  }
}
