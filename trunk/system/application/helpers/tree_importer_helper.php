<?php

function import_trees_xml_node($top, $tree_model, $rank_model, $tax_model)
{
  $stats = array();
  
  foreach($top->childNodes as $child) {
    if($child->nodeName != 'tree') {
      continue;
    }
    
    $this_stat = import_tree_xml_node($child, $tree_model, $rank_model, $tax_model);
    if($this_stat) {
      $stats[] = $this_stat;
    }
  }
  
  return $stats;
}

function import_tree_xml_file($tree_model, $rank_model, $tax_model, $file)
{
  $xmlDoc = new DOMDocument();
  if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
    return null;
  }
  
  return import_tree_xml_node($xmlDoc->documentElement, $tree_model, $rank_model, $tax_model);
}
 
function import_tree_xml_node($top, $tree_model, $rank_model, $tax_model)
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
  
  if($tree_model->has_name($name)) {
    $id = $tree_model->get_id_by_name($name);
    $stats['mode'] = 'edit';
  } else {
    $id = $tree_model->add($name);
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
    __import_tree_tax($tax_model, $rank_model, $nodes, $id, null, $stats);
  }
  
  return $stats;
}

function __import_tree_tax($model, $rank_model, $node, $tree, $parent, &$stats)
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
    
    if($model->has_name_tree($name, $tree)) {
      $id = $model->get_name_tree_id($name, $tree);
      if(!$id) {
        continue;
      }
      $model->edit_parent($id, $parent);
      $stats['old_tax'] = $stats['old_tax'] + 1;
    } else {
      $id = $model->add($name, null, $tree, $parent);
      if(!$id) {
        continue;
      }
      $stats['new_tax'] = $stats['new_tax'] + 1;
    }
    
    $rank_node = find_xml_child($child, 'rank');
    if(!__add_tree_rank($model, $rank_model, $id, find_xml_child($child, 'rank'), $stats)) {
      $model->edit_rank($id, null);
    }
    
    __import_tree_tax($model, $rank_model, $child, $tree, $id, $stats);
  }
}

function __add_tree_rank($model, $rank_model, $id, $rank_node, &$stats)
{
  if(!$rank_node) {
    return false;
  }
  
  $rank = xmlspecialchars_decode(trim($rank_node->textContent));
  
  if($rank) {
    $rank_id = $rank_model->get_id_name($rank);
    if(!$rank_id) {
      $rank_id = $rank_model->add($rank);
      $stats['new_ranks'] = $stats['new_ranks'] + 1;
    }
    
    if($rank_id) {
      $model->edit_rank($id, $rank_id);
    }
    
    return true;
  }
  
  return false;
}