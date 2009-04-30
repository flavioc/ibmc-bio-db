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

  function get_by_name($name)
  {
    return $this->get_row('name', $name);
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

  function _get_search_sql($name, $rank, $tree, $start = null, $size = null)
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
    $sql .= sql_limit($start, $size);

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

    return $sql;
  }

  function _search_query($name, $rank, $tree, $start = null, $size = null)
  {
    $search = $this->_get_search_sql($name, $rank, $tree, $start, $size);
    $sql =  "$search ORDER BY name";
    return $this->db->query($sql);
  }

  function search($name, $rank, $tree, $start = null, $size = null, $ordering = array())
  {
    return $this->search_field('*', $name, $rank, $tree, $start, $size, $ordering);
  }

  function search_field($field, $name, $rank, $tree, $start = null, $size = null, $ordering = array())
  {
    $order = $this->get_order_by($ordering, 'name', 'asc');
    $order_field = $order[0];
    $order_type = $order[1];
    $order_str = " ORDER BY $order_field $order_type ";
    $search = $this->_get_search_sql($name, $rank, $tree, $start, $size);
    $limit = sql_limit($start, $size);
    $sql = "SELECT $field FROM (taxonomy_info NATURAL JOIN ($search) AS dderiv) $order_str";

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
    $this->db->where('rank_id', $rank);

    return $this->count_total();
  }

  function count_tree($tree)
  {
    $this->db->where('tree_id', $tree);

    return $this->count_total();
  }

  function delete($id)
  {
    // delete all names
    $this->db->trans_start();
    $this->delete_by_field('tax_id', $id, 'taxonomy_name');
    $this->delete_id($id);
    $this->db->trans_complete();
  }

  function __get_children($tax, $tree)
  {
    $tree_str = "";
    if($tree) {
      $tree_str = "tree_id = $tree AND";
    }

    if($tax == null) {
      return "WHERE $tree_str ((parent_id IS NULL AND import_id IS NULL) OR (parent_id IS NULL AND import_id IS NOT NULL AND import_id = import_parent_id))";
    } else {
      $import_id = $this->get_import_id($tax);
      $sql = "WHERE $tree_str ((parent_id IS NOT NULL AND parent_id = $tax) OR ";
      if($import_id) {
        $sql .= "(import_parent_id IS NOT NULL AND import_parent_id = $import_id AND import_parent_id <> import_id))";
      } else {
        $sql .= "FALSE)";
      }

      return $sql;
    }
  }

  function get_taxonomy_children($tax, $tree, $start = null, $size = null)
  {
    $sql = "SELECT * FROM taxonomy_info " . $this->__get_children($tax, $tree) .
      " ORDER BY name ";

    $sql .= sql_limit($start, $size);

    return $this->rows_sql($sql);
  }

  function count_taxonomy_children($tax, $tree)
  {
    $sql = "SELECT count(id) AS total FROM taxonomy " . $this->__get_children($tax, $tree);
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

