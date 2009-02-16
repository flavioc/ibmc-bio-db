<?php

class Taxonomy_tree_model extends BioModel
{
  function Taxonomy_tree_model()
  {
    parent::BioModel('taxonomy_tree');
  }

  function get_trees()
  {
    $this->db->order_by('name');
    return $this->get_all();
  }

  function has_name($name)
  {
    return $this->has_field('name', $name);
  }

  function add($name)
  {
    return $this->insert_data_with_history(array('name' => $name));
  }

  function edit($id, $new_name)
  {
    if($this->has_name($new_name)) {
      return false;
    }

    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'name', $new_name);

    $this->db->trans_complete();

    return true;
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }
}

