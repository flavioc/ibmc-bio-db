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

  function _get_search_sql($name, $rank, $start = null, $size = null)
  {
    $nocase = false;

    if($nocase) {
      $lower_name = strtolower($name);
    } else {
      $lower_name = $name;
    }

    $sql = " FROM (SELECT *
                  FROM taxonomy_parent_rank ";

    if($rank) {
      $sql .= "WHERE rank_id = $rank";
    }

    $sql .= ") AS a";
    
    $sql .= " NATURAL JOIN
              (SELECT DISTINCT id
              FROM taxonomy_all_names AS b
              WHERE ";

    if($nocase) {
      $sql .= "LCASE(b.name)";
    } else {
      $sql .= "b.name";
    }

    $sql .= " LIKE '$lower_name%') AS c
ORDER BY name";
  
    if($start != null && $size != null) {
      $sql .= " LIMIT $start, $size";
    }

    return $sql;
  }

  function search($name, $rank, $start = null, $size = null)
  {
    $query = $this->db->query('SELECT * ' . $this->_get_search_sql($name, $rank, $start, $size));

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

  function get_import_id($id)
  {
    return $this->get_id_by_field('import_id', $id);
  }

  function ensure_existance($import_id, $import_parent_id, $rank, $name)
  {
    $id = $this->get_import_id($import_id);
    
    if($id != null) {
      /*
      $data = array(
        'name' => $name,
        'rank_id' => $rank,
        'import_parent_id' => $import_parent_id,
      );

      $this->edit_data($id, $data);
*/
      return $id;
    } else {
      $data = array(
        'name' => $name,
        'rank_id' => $rank,
        'import_id' => $import_id,
        'import_parent_id' => $import_parent_id,
      );

      return $this->insert_data($data);
    }
  }

  function get_import_parented()
  {
    $this->db->select('id, import_parent_id');
    $this->db->where('import_parent_id IS NOT NULL');

    return $this->db->get($this->table)->result_array();
  }

  function fix_imported_parent($id, $imported_parent)
  {
    $parent_id = $this->get_import_id($imported_parent);

    $this->edit_field($id, 'parent_id', $parent_id);

    return $parent_id != null;
  }
}

