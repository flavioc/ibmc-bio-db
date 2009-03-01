<?php

class Taxonomy_rank_model extends BioModel
{
  function Taxonomy_rank_model()
  {
    parent::BioModel('taxonomy_rank');
  }

  function get($id)
  {
    return $this->get_row('rank_id', $id, '*', 'taxonomy_rank_info_history');
  }

  function get_ranks($size = null, $start = null)
  {
    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->order_by('rank_name');
    $this->db->select('history_id, rank_id, rank_name, rank_parent_id, rank_parent_name, update_user_id, update, user_name');

    return $this->get_all('taxonomy_rank_info_history');
  }

  function get_rank_id($rank)
  {
    $id = $this->get_id_by_field('name', $rank);

    if($id == null) {
      return $this->add($rank);
    } else {
      return $id;
    }
  }

  function get_child($id)
  {
    $data = $this->get_all_by_field('parent_id', $id);

    if(count($data) == 0) {
      return null;
    } else {
      return $data[0]['id'];
    }
  }

  function edit_name($id, $new_name)
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

  function edit_parent($id, $new_parent)
  {
    if($this->parent_used($new_parent, $id)) {
      return false;
    }

    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'parent_id', $new_parent);

    $this->db->trans_complete();

    return true;
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function has_name($name)
  {
    return $this->has_field('name', $name);
  }

  function add($name, $parent = null)
  {
    if($parent) {
      if($this->parent_used($parent)) {
        return null;
      }
    }

    $data = array(
      'name' => $name,
      'parent_id' => $parent,
    );

    return $this->insert_data_with_history($data);
  }

  function add_array($arr)
  {
    foreach($arr as $name) {
      if(!$this->has($name)) {
        $this->add($name);
      }
    }
  }

  function get_total()
  {
    return $this->count_total();
  }

  function get_parent_name($id)
  {
    $row = $this->get_row('rank_id', $id, 'rank_parent_name',
      'taxonomy_rank_info');

    return $row['rank_parent_name'];
  }

  function parent_used($parent, $except_id = null)
  {
    if($except_id) {
      $this->db->where("id != $except_id");
    }

    return $this->has_field('parent_id', $parent);
  }
}

