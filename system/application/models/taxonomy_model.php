<?php

class Taxonomy_model extends BioModel
{
  function Taxonomy_model()
  {
    parent::BioModel('taxonomy');
  }

  function add($name, $rank)
  {
    $data = array(
      'name' => $name,
      'rank_id' => $rank,
    );

    return $this->insert_data_with_history($data);
  }

  function get($id)
  {
    $query = $this->db->query('SELECT id, name, rank_id, parent_id, rank_name
                              FROM taxonomy NATURAL JOIN (SELECT name as rank_name, id as rank_id
                                                          FROM taxonomy_rank) t2
                              WHERE id = ' . $id);

    if(!$query || $query->num_rows() != 1) {
      return null;
    }

    $data = $query->row_array();

    return $data;
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function edit_name($id, $name)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
  }

  function edit_rank($id, $rank_id)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'rank_id', $rank_id);

    $this->db->trans_complete();
  }

  function edit_parent($id, $parent_id)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'parent_id', $parent_id);

    $this->db->trans_complete();
  }

  function _get_search_sql($name, $rank)
  {
    $lower_name = strtolower($name);

    $sql = " FROM taxonomy_parent_rank a
      WHERE a.id IN (SELECT DISTINCT id
                    FROM taxonomy_all_names AS b
                    WHERE LCASE(b.name) LIKE '%$lower_name%')";

    if($rank) {
      $sql .= " AND a.rank_id = $rank";
    }

    $sql .= ' ORDER BY name ';

    return $sql;
  }

  function search($name, $rank)
  {
    $query = $this->db->query('SELECT * ' . $this->_get_search_sql($name, $rank));

    $data = $query->result_array();

    return $data;
  }

  function search_total($name, $rank)
  {
    $query = $this->db->query('SELECT count(id) AS total' . $this->_get_search_sql($name, $rank));

    $data = $query->row_array();

    return $data['total'];
  }

  function delete($id)
  {
    // delete all names
    $this->db->trans_start();
    $this->delete_by_field('tax_id', $id, 'taxonomy_name');
    $this->delete_id($id);
    $this->db->trans_complete();
  }
}

