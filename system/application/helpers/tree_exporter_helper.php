<?php

function export_tree_xml($tree_model, $tax_model, $tree_id)
{
  $ret = "<tree>\n";
  
  $name = xmlspecialchars($tree_model->get_name($tree_id));
  $ret .= "\t<name>$name</name>\n";
  
  $tax_xml = __export_tax_xml($tax_model, $tree_id, null);
  $ret .= "\t<nodes>\n$tax_xml\t</nodes>\n";
  
  return "$ret</tree>\n";
}

function __export_tax_xml($model, $tree, $parent, $count = 2)
{
  $childs = $model->get_taxonomy_children($parent, $tree);
  
  $ret = '';
  $next_count = $count + 1;
  
  foreach($childs as &$child) {
    $ret .= str_repeat("\t", $count) . "<taxonomy>\n";
    $ret .= str_repeat("\t", $next_count) . "<name>" . xmlspecialchars($child['name']) . "</name>\n";
    
    $rank = $child['rank_name'];
    if($rank) {
      $ret .= str_repeat("\t", $next_count) . "<rank>" . xmlspecialchars($rank) . "</rank>\n";
    }
    
    $ret .= __export_tax_xml($model, $tree, $child['id'], $next_count);
    $ret .= str_repeat("\t", $count) . "</taxonomy>\n"; 
  }
  
  return $ret;
}