<?php

class Taxonomy_name_type_model extends BioModel
{
  function Taxonomy_name_type_model()
  {
    parent::BioModel('taxonomy_name_type');
  }

  function get($id)
  {
    return $this->get_id($id);
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function add($name)
  {
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 512 || $this->has_name($name)) {
      return false;
    }
    
    return $this->insert_data(array('name' => $name));
  }

  function get_type_id($type)
  {
    $id = $this->get_id_by_field('name', $type);

    if($id == null) {
      return $this->add($type);
    } else {
      return $id;
    }
  }
  
  function has_name($name)
  {
    return $this->has_field('name', $name);
  }
}