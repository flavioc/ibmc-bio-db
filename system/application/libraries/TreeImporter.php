<?php

class TreeImporter
{
  private $tree_model = null;
  private $rank_model = null;
  private $tax_model = null;
  
  function TreeImporter()
  {
    $this->tree_model = load_ci_model('taxonomy_tree_model');
    $this->rank_model = load_ci_model('taxonomy_rank_model');
    $this->tax_model = load_ci_model('taxonomy_model');
  }
  
  public function import_group_xml_node($top)
  {
    $stats = array();

    foreach($top->childNodes as $child) {
      if($child->nodeName != 'tree') {
        continue;
      }

      $this_stat = $this->import_single_xml_node($child);
      if($this_stat) {
        $stats[] = $this_stat;
      }
    }

    return $stats;
  }

  public function import_xml($file)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      return null;
    }

    return $this->import_single_xml_node($xmlDoc->documentElement);
  }

  public function import_single_xml_node($top)
  {
    if(!$top || $top->nodeName != 'tree') {
      return null;
    }

    $name_node = find_xml_child($top, 'name');
    if(!$name_node) {
      return null;
    }


    $name = trim($name_node->textContent);
    if(!$name) {
      return null;
    }

    $name = xmlspecialchars_decode($name);
    $stats = array('name' => $name);

    if($this->tree_model->has_name($name)) {
      $id = $this->tree_model->get_id_by_name($name);
      $stats['mode'] = 'edit';
    } else {
      $id = $this->tree_model->add($name);
      $stats['mode'] = 'add';
    }

    if(!$id) {
      return null;
    }

    $nodes = find_xml_child($top, 'nodes');

    $stats['id'] = $id;
    $stats['new_ranks'] = 0;
    $stats['new_tax'] = 0;
    $stats['old_tax'] = 0;

    if($nodes) {
      $this->__import_tax($nodes, $id, null, $stats);
    }

    return $stats;
  }

  private function __import_tax($node, $tree, $parent, &$stats)
  {
    foreach($node->childNodes as $child) {
      if($child->nodeName != 'taxonomy') {
        continue;
      }

      $name_node = find_xml_child($child, 'name');
      if(!$name_node) {
        continue;
      }

      $name = trim($name_node->textContent);
      if(!$name) {
        continue;
      }

      $name = xmlspecialchars_decode($name);

      if($this->tax_model->has_name_tree($name, $tree)) {
        $id = $this->tax_model->get_name_tree_id($name, $tree);
        if(!$id) {
          continue;
        }
        $this->tax_model->edit_parent($id, $parent);
        $stats['old_tax'] = $stats['old_tax'] + 1;
      } else {
        $id = $this->tax_model->add($name, null, $tree, $parent);
        if(!$id) {
          continue;
        }
        $stats['new_tax'] = $stats['new_tax'] + 1;
      }

      $rank_node = find_xml_child($child, 'rank');
      if(!$this->__add_tree_rank($id, find_xml_child($child, 'rank'), $stats)) {
        $this->tax_model->edit_rank($id, null);
      }

      $this->__import_tax($child, $tree, $id, $stats);
    }
  }

  private function __add_tree_rank($id, $rank_node, &$stats)
  {
    if(!$rank_node) {
      return false;
    }

    $rank = xmlspecialchars_decode(trim($rank_node->textContent));

    if($rank) {
      $rank_id = $this->rank_model->get_id_name($rank);
      if(!$rank_id) {
        $rank_id = $this->rank_model->add($rank);
        $stats['new_ranks'] = $stats['new_ranks'] + 1;
      }

      if($rank_id) {
        $this->tax_model->edit_rank($id, $rank_id);
      }

      return true;
    }

    return false;
  }
}