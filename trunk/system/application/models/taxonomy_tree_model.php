<?php

class Taxonomy_tree_model extends BioModel
{
  function Taxonomy_tree_model()
  {
    parent::BioModel('taxonomy_tree');
  }

  public function has_tree($id)
  {
    return $this->has_id($id);
  }

  private function __select()
  {
    $this->db->select('tree_id AS id, tree_name AS name, `update`, update_user_id, user_name');
  }

  private function __filter($filtering = array())
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if(!sql_is_nothing($name)) {
        $name = $this->db->escape($name);
        $this->db->where("tree_name REGEXP $name");
      }
    }

    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
    }
  }

  public function get_trees($filtering = array(), $ordering = array(), $select = null)
  {
    // this filtering must be done before everything else because the queries may get mangled
    if(array_key_exists('no_ncbi', $filtering)) {
      if($filtering['no_ncbi']) {
        $ncbi = $this->get_ncbi_id();
        $this->db->where("tree_id <> $ncbi");
      }
    }
    
    if($select) {
      $this->db->select($select);
    } else {
      $this->__select();
    }
    $this->order_by($ordering, 'tree_name', 'asc');
    $this->__filter($filtering);

    return $this->get_all('taxonomy_tree_info_history');
  }

  public function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'taxonomy_tree_info_history', 'tree_id');
  }

  public function has_name($name)
  {
    return $this->has_field('name', $name);
  }

  public function add($name)
  {
    $name = trim($name);
    
    if(strlen($name) <= 0 || strlen($name) > 255 || $this->has_name($name)) {
      return false;
    }
    
    return $this->insert_data_with_history(array('name' => $name));
  }

  public function edit($id, $new_name)
  {
    $new_name = trim($new_name);
    
    if(strlen($new_name) <= 0 || strlen($new_name) > 255 || $this->has_name($new_name)) {
      return false;
    }

    $this->db->trans_start();

    $ret = $this->update_history($id);
    
    if($ret) {
      $ret = $this->edit_field($id, 'name', $new_name);
    }

    $this->db->trans_complete();

    return $ret;
  }

  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }
  
  public function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }
  
  public function get_ncbi_id()
  {
    return $this->get_id_by_name('NCBI');
  }
  
  public function delete_all_custom()
  {
    $ncbi = $this->get_ncbi_id();
    
    $this->db->where("id <> $ncbi");
    return $this->delete_rows();
  }
}