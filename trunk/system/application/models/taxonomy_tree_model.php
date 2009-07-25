<?php

class Taxonomy_tree_model extends BioModel
{
  function Taxonomy_tree_model()
  {
    parent::BioModel('taxonomy_tree');
  }

  function has_tree($id)
  {
    return $this->has_id($id);
  }

  function __select()
  {
    $this->db->select('tree_id AS id, tree_name AS name, update, update_user_id, user_name');
  }

  function __filter($filtering = array())
  {
    if(count($filtering) == 0) {
      return;
    }

    $name = $filtering['name'];
    if(!sql_is_nothing($name)) {
      $this->db->like('tree_name', "%$name%");
    }

    $user = $filtering['user'];
    if(!sql_is_nothing($user)) {
      $this->db->where('update_user_id', $user);
    }
  }

  function get_trees($filtering = array(), $ordering = array())
  {
    $this->__select();
    $this->order_by($ordering, 'name', 'asc');
    $this->__filter($filtering);

    return $this->get_all('taxonomy_tree_info_history');
  }

  function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'taxonomy_tree_info_history', 'tree_id');
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
    if($new_name == '' || $this->has_name($new_name)) {
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

