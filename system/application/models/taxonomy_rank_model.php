<?php

class Taxonomy_rank_model extends BioModel
{
  function Taxonomy_rank_model()
  {
    parent::BioModel('taxonomy_rank');
  }

  public function has_rank($id)
  {
    return $this->has_id($id);
  }

  public function get($id)
  {
    return $this->get_row('rank_id', $id, 'taxonomy_rank_info_history');
  }

  public function get_unparented_ranks($id = null)
  {
    $this->db->order_by('name');

    return parent::get_all();
  }

  public function get_ranks($size = null, $start = null,
    $filtering = array(),
    $ordering = array())
  {
    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->select('history_id, rank_id, rank_name, rank_parent_id, rank_parent_name, update_user_id, `update`, user_name');
    $this->order_by($ordering, 'rank_name', 'asc');
    $this->__filter($filtering);

    return $this->get_all('taxonomy_rank_info_history');
  }
  
  public function get_id_name($rank)
  {
    return $this->get_id_by_field('name', $rank);
  }

  public function get_rank_id($rank)
  {
    $id = $this->get_id_name($rank);

    if($id == null) {
      return $this->add($rank);
    } else {
      return $id;
    }
  }

  public function get_first_child($id)
  {
    $data = $this->get_all_by_field('parent_id', $id);

    if(count($data) == 0) {
      return null;
    } else {
      return $data[0]['id'];
    }
  }

  public function get_children_names($id)
  {
    $this->db->select('name');
    $data = $this->get_all_by_field('parent_id', $id);

    $ret = array();

    if($data) {
      foreach($data as $el) {
        $ret[] = $el['name'];
      }
    }

    return $ret;
  }

  public function edit_name($id, $name)
  {
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 128 || $this->has_name($name)) {
      return false;
    }

    $this->db->trans_start();

    $ret = $this->update_history($id);
    if($ret) {
      $ret = $this->edit_field($id, 'name', $name);
    }

    $this->db->trans_complete();

    return $ret;
  }

  public function edit_parent($id, $new_parent)
  {
    $this->db->trans_start();

    $ret = $this->update_history($id);
    if($ret) {
      $ret = $this->edit_field($id, 'parent_id', $new_parent);
    }

    $this->db->trans_complete();

    return true;
  }

  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  public function get_parent($id)
  {
    return $this->get_field($id, 'parent_id');
  }

  public function has_name($name)
  {
    return $this->has_field('name', $name);
  }

  public function add($name, $parent = null)
  {
    if($parent) {
      if($this->parent_used($parent)) {
        return null;
      }
    }

    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 128 || $this->has_name($name)) {
      return false;
    }
    
    $data = array(
      'name' => $name,
      'parent_id' => $parent,
    );

    return $this->insert_data_with_history($data);
  }

  /* add multiple ranks */
  public function add_array($arr)
  {
    $ret = true;
    foreach($arr as $name) {
      if(!$this->has($name)) {
        if(!$this->add($name)) {
          $ret = false;
        }
      }
    }
    
    return $ret;
  }

  private function __filter($filtering)
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if(!sql_is_nothing($name)) {
        $name = $this->db->escape($name);
        $this->db->where("rank_name REGEXP $name");
      }
    }

    if(array_key_exists('parent_name', $filtering)) {
      $parent_name = $filtering['parent_name'];
      if(!sql_is_nothing($parent_name)) {
        $parent_name = $this->db->escape($parent_name);
        $this->db->where("rank_parent_name REGEXP $parent_name");
      }
    }

    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
    }
  }

  public function get_total($filtering = array())
  {
    $this->__filter($filtering);
    return $this->count_total('taxonomy_rank_info_history');
  }

  public function get_parent_name($id)
  {
    return $this->get_field($id, 'rank_parent_name', 'taxonomy_rank_info', 'rank_id');
  }

  public function parent_used($parent, $except_id = null)
  {
    if($except_id) {
      $this->db->where("id != $except_id");
    }

    return $this->has_field('parent_id', $parent);
  }
  
  public function delete_all_custom()
  {
    $this->db->where('is_default IS FALSE');
    return $this->delete_rows();
  }
}