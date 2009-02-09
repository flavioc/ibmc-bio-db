<?php

class Taxonomy_rank_model extends BioModel
{
  function Taxonomy_rank_model()
  {
    parent::BioModel('taxonomy_rank');
  }

  function get_ranks()
  {
    $this->db->order_by('name');
    return $this->get_all();
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

  function has_name($name)
  {
    return $this->has_field('name', $name);
  }

  function add($name)
  {
    return $this->insert_data_with_history(array('name' => $name));
  }

  function add_array($arr)
  {
    foreach($arr as $name) {
      if(!$this->has($name)) {
        $this->add($name);
      }
    }
  }
}

