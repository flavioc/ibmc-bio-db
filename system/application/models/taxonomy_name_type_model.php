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
}
