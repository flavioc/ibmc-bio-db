<?php

function export_trees_xml($tree_model, $tax_model, $trees, $tab = 0)
{
  $t = tabs($tab);
  $ret = "$t<trees>\n";
  
  foreach($trees as &$tree) {
    $ret .= export_tree_xml($tree_model, $tax_model, $tree['id'], $tab + 1);
  }
  
  return "$ret$t</trees>\n";
}

function export_tree_xml($tree_model, $tax_model, $tree_id, $tab = 0)
{
  $t = tabs($tab);
  
  $ret = "$t<tree>\n";
  
  $name = xmlspecialchars($tree_model->get_name($tree_id));
  $ret .= "$t\t<name>$name</name>\n";
  
  $tax_xml = __export_tax_xml($tax_model, $tree_id, null, $tab + 2);
  $ret .= "$t\t<nodes>\n$tax_xml$t\t</nodes>\n";
  
  return "$ret$t</tree>\n";
}

function __export_tax_xml($model, $tree, $parent, $count = 2)
{
  $childs = $model->get_taxonomy_children($parent, $tree);
  
  $ret = '';
  $next_count = $count + 1;
  
  foreach($childs as &$child) {
    $ret .= tabs($count) . "<taxonomy>\n";
    $ret .= tabs($next_count) . "<name>" . xmlspecialchars($child['name']) . "</name>\n";
    
    $rank = $child['rank_name'];
    if($rank) {
      $ret .= tabs($next_count) . "<rank>" . xmlspecialchars($rank) . "</rank>\n";
    }
    
    $ret .= __export_tax_xml($model, $tree, $child['id'], $next_count);
    $ret .= tabs($count) . "</taxonomy>\n"; 
  }
  
  return $ret;
}