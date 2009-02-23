<?php

class Taxonomy_rank_model extends BioModel
{
  function Taxonomy_rank_model()
  {
    parent::BioModel('taxonomy_rank');
  }

  function get($id)
  {
    return $this->get_row('rank_id', $id, '*', 'taxonomy_rank_info');
  }

  function get_ranks($size = null, $start = null)
  {
    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->order_by('rank_name');

    return $this->get_all('taxonomy_rank_info');
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
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'parent_id', $new_parent);

    $this->db->trans_complete();
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
}

