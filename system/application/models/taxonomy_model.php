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
    
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) >= 512) {
      return false;
    }
    
    if($parent && !$this->has_id($parent)) {
      return false;
    }
    
    $tree_model = $this->load_model('taxonomy_tree_model');
    if($tree && !$tree_model->has_id($tree)) {
      return false;
    }
    
    $rank_model = $this->load_model('taxonomy_rank_model');
    if($rank && !$rank_model->has_id($rank)) {
      return false;
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
  
  function has_name_tree($name, $tree)
  {
    $this->db->where('tree_id', $tree);
    return $this->has_field('name', $name);
  }
  
  function get_name_tree_id($name, $tree)
  {
    $this->db->where('tree_id', $tree);
    return $this->get_id_by_field('name', $name);
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
    $name = trim($name);
    
    if(strlen($name) <= 0 || strlen($name) > 512) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
    
    return $ret;
  }

  function edit_rank($id, $rank_id)
  {
    $rank_model = $this->load_model('taxonomy_rank_model');
    
    if($rank_id && !$rank_model->has_id($rank_id)) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'rank_id', $rank_id);

    $this->db->trans_complete();
    
    return $ret;
  }

  function edit_tree($id, $tree_id)
  {
    $tree_model = $this->load_model('taxonomy_tree_model');
    
    if($tree_id && !$tree_model->has_id($tree_id)) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);

    $ret = $this->edit_field($id, 'tree_id', $tree_id);
    
    if($ret) {
      // reset parent field
      $ret = $this->edit_field($id, 'parent_id', NULL);
    }

    $this->db->trans_complete();
    
    return $ret;
  }

  function edit_parent($id, $parent_id)
  {
    if($parent_id && $this->has_id($parent_id)) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'parent_id', $parent_id);

    $this->db->trans_complete();
    
    return $ret;
  }

  function _get_search_sql($name, $rank, $tree, $start = null, $size = null)
  {
    $condition = false;
    $sql = "SELECT id FROM taxonomy_info";
    
    if($tree && is_numeric($tree)) {
      $sql .= " WHERE tree_id = $tree";
      $condition = true;
    }

    if($rank && is_numeric($rank)) {
      if($condition) {
        $sql .= " AND";
      } else {
        $condition = true;
        $sql .= " WHERE";
      }
      
      $sql .= " rank_id = $rank";
    }

    if($name != '') {
      if($condition) {
        $sql .= " AND";
      } else {
        $condition = true;
        $sql .= " WHERE";
      }
      $name = $this->db->escape($name);
      $sql .= " name REGEXP $name";
    }
    
    $sql .= sql_limit($start, $size);
    
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
    $order = $this->get_order_sql($ordering, 'name', 'asc');
    $search = $this->_get_search_sql($name, $rank, $tree, $start, $size);
    $limit = sql_limit($start, $size);
    $sql = "SELECT $field FROM (taxonomy_info NATURAL JOIN ($search) AS dderiv) $order";

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
    if($tax != null && !is_numeric($tax)) {
      return null;
    }
    
    if($tree != null && !is_numeric($tree)) {
      return null;
    }
    
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
    $children_sql = $this->__get_children($tax, $tree);
    if(!$children_sql) {
      return array();
    }
    
    $sql = "SELECT * FROM taxonomy_info $children_sql ORDER BY name ";

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

