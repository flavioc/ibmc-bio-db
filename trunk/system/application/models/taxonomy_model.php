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
    $this->db->select('id, name, rank_id, tree_id, rank_name, tree_name, update_user_id, update, user_name');

    return $this->get_id($id, 'taxonomy_info_history');
  }

  function get_parent($id)
  {
    $import_parent_id = $this->get_import_parent_id($id);

    if($import_parent_id) {
      $this->db->select('id AS parent_id, name AS parent_name');
      return $this->get_row('import_id', $import_parent_id);
    } else {
      $parent_id = $this->get_field($id, 'parent_id');
      $this->db->select('id AS parent_id, name AS parent_name');
      $ret = $this->get_id($parent_id);
      if($ret) {
        return $ret;
      } else {
        return array('parent_id' => NULL, 'parent_name' => NULL);
      }
    }
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

  function get_tree($id)
  {
    return $this->get_field($id, 'tree_id');
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

    $where_name_sql = "";
    if($nocase) {
      $where_name_sql .= "LCASE(name)";
    } else {
      $where_name_sql .= "name";
    }
    $where_name_sql .= " LIKE '%$lower_name%'";

    $sql = "SELECT id ";

    $sql .= " FROM taxonomy_info WHERE TRUE ";
    if($tree) {
      $sql .= " AND tree_id = $tree ";
    }

    if($rank) {
      $sql .= " AND rank_id = $rank ";
    }

    $sql .= " AND $where_name_sql ";

    /*
    $sql .= " UNION SELECT tax_id AS id FROM taxonomy_name_tax WHERE TRUE ";

    if($tree) {
      $sql .= " AND tree_id = $tree ";
    }

    if($rank) {
      $sql .= " AND rank_id = $rank ";
    }
    $sql .= " AND $where_name_sql ";
     */

    $sql .= sql_limit($start, $size);

    return $sql;
  }

  function _search_query($name, $rank, $tree, $start = null, $size = null)
  {
    $search = $this->_get_search_sql($name, $rank, $tree);
    $sql =  $this->_get_search_sql($name, $rank, $tree) . ' ORDER BY name' . sql_limit($start, $size);
    return $this->db->query($sql);
  }

  function search($name, $rank, $tree, $start = null, $size = null)
  {
    return $this->search_field('*', $name, $rank, $tree, $start, $size);
  }

  function search_field($field, $name, $rank, $tree, $start = null, $size = null)
  {
    $search = $this->_get_search_sql($name, $rank, $tree, $start, $size);
    $sql = "SELECT $field FROM (taxonomy_info NATURAL JOIN ($search) AS dderiv ) ORDER BY name";

    return $this->rows_sql($sql);
  }

  function search_total($name, $rank, $tree)
  {
    $search = $this->_get_search_sql($name, $rank, $tree);
    $sql = "SELECT count(id) AS total FROM ($search) AS C";

    return $this->total_sql($sql);
  }

  function count_rank($rank)
  {
    $this->db->from($this->table);
    $this->db->where('rank_id', $rank);

    return intval($this->db->count_all_results());
  }

  function count_tree($tree)
  {
    $this->db->from($this->table);
    $this->db->where('tree_id', $tree);

    return intval($this->db->count_all_results());
  }

  function delete($id)
  {
    // delete all names
    $this->db->trans_start();
    $this->delete_by_field('tax_id', $id, 'taxonomy_name');
    $this->delete_id($id);
    $this->db->trans_complete();
  }

  function __get_childs($tax, $tree)
  {
    if($tax == null) {
      return "WHERE tree_id = $tree AND ((parent_id IS NULL AND import_id IS NULL) OR (parent_id IS NULL AND import_id IS NOT NULL AND import_id = import_parent_id))";
    } else {
      $import_id = $this->get_import_id($tax);
      $sql = "WHERE tree_id = $tree AND ((parent_id IS NOT NULL AND parent_id = $tax) OR ";
      if($import_id) {
        $sql .= "(import_parent_id IS NOT NULL AND import_parent_id = $import_id AND import_parent_id <> import_id))";
      } else {
        $sql .= "FALSE)";
      }

      return $sql;
    }
  }

  function get_taxonomy_childs($tax, $tree, $start = null, $size = null)
  {

    $sql = "SELECT * FROM taxonomy_info " . $this->__get_childs($tax, $tree) .
      " ORDER BY name ";

    $sql .= sql_limit($start, $size);

    return $this->rows_sql($sql);
  }

  function count_taxonomy_childs($tax, $tree)
  {
    $sql = "SELECT count(id) AS total FROM taxonomy " . $this->__get_childs($tax, $tree);
    return $this->total_sql($sql);
  }

  function get_import_id($id)
  {
    return $this->get_field($id, 'import_id');
  }

  function get_import_parent_id($id)
  {
    return $this->get_field($id, 'import_parent_id');
  }
}

