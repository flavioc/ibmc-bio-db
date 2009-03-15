<?php

class Taxonomy_model extends BioModel
{
  function Taxonomy_model()
  {
    parent::BioModel('taxonomy');
  }

  function add($name, $rank, $tree, $parent = null)
  {
    if(!$parent) {
      $parent = null;
    }

    $data = array(
      'name' => $name,
      'rank_id' => $rank,
      'tree_id' => $tree,
      'parent_id' => $parent,
    );

    return $this->insert_data_with_history($data);
  }

  function get($id)
  {
    $this->db->select('id, name, rank_id, tree_id, parent_id, parent_name, rank_name, tree_name, update_user_id, update, user_name');

    return $this->get_id($id, 'taxonomy_info_history');
  }

  function has_taxonomy($id)
  {
    return $this->has_id($id);
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function get_rank($id)
  {
    return $this->get_field($id, 'rank_id');
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

  function edit_tree($id, $tree_id)
  {
    $this->db->trans_start();

    $this->update_history($id);

    $this->edit_field($id, 'tree_id', $tree_id);
    // reset parent field
    $this->edit_field($id, 'parent_id', NULL);

    $this->db->trans_complete();
  }

  function edit_parent($id, $parent_id)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'parent_id', $parent_id);

    $this->db->trans_complete();
  }

  function _get_search_sql($name, $rank, $tree, $start = 0, $size = null)
  {
    $nocase = false;

    if($nocase) {
      $lower_name = strtolower($name);
    } else {
      $lower_name = $name;
    }

    $sql = " FROM (SELECT *
      FROM taxonomy_info
      WHERE 1";

    if($tree) {
      $sql .= " AND tree_id = $tree";
    } else {
      $sql .= " AND tree_id IS NULL";
    }

    if($rank) {
      $sql .= " AND rank_id = $rank";
    } else {
      $sql .= " AND rank_id IS NULL";
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

    if($start == null) {
      $start = 0;
    }
  
    if($size != null) {
      $sql .= " LIMIT $start, $size";
    }

    return $sql;
  }

  function _search_query($name, $rank, $tree, $start = null, $size = null)
  {
    return $this->db->query('SELECT * ' . $this->_get_search_sql($name, $rank, $tree, $start, $size));
  }

  function search($name, $rank, $tree, $start = null, $size = null)
  {
    $query = $this->_search_query($name, $rank, $tree, $start, $size);

    $data = $query->result_array();

    return $data;
  }

  function search_field($field, $name, $rank, $tree, $start = null, $size = null)
  {
    $query = $this->db->query("SELECT $field " . $this->_get_search_sql($name, $rank,
      $tree, $start, $size));

    $data = $query->result_array();

    return $data;
  }

  function search_total($name, $rank, $tree)
  {
    $query = $this->_search_query($name, $rank, $tree);

    return $query->num_rows();
  }

  function count_rank($rank)
  {
    $this->db->from($this->table);
    $this->db->where('rank_id', $rank);

    return $this->db->count_all_results();
  }

  function count_tree($tree)
  {
    $this->db->from($this->table);
    $this->db->where('tree_id', $tree);

    return $this->db->count_all_results();
  }

  function delete($id)
  {
    // delete all names
    $this->db->trans_start();
    $this->delete_by_field('tax_id', $id, 'taxonomy_name');
    $this->delete_id($id);
    $this->db->trans_complete();
  }

  function get_taxonomy_childs($tax, $tree, $start = null, $size = null)
  {
    $this->db->where('tree_id', $tree);
    $this->db->where('parent_id', $tax);

    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->order_by('name');
    return $this->get_all('taxonomy_info');
  }

  function count_taxonomy_childs($tax, $tree)
  {
    $this->db->where('tree_id', $tree);
    $this->db->where('parent_id', $tax);

    return $this->count_total();
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

