<?php

class TreeExporter
{
  private $tree_model = null;
  private $tax_model = null;
  
  function TreeExporter()
  {
    $CI =& get_instance();
    $CI->load->model('taxonomy_tree_model', '', true);
    $CI->load->model('taxonomy_model', '', true);
    $this->tree_model = $CI->taxonomy_tree_model;
    $this->tax_model = $CI->taxonomy_model;
  }
  
  public function export_group($trees, $tab = 0)
  {
    $t = tabs($tab);
    $ret = "$t<trees>\n";

    foreach($trees as &$tree) {
      $ret .= $this->export_one($tree['id'], $tab + 1);
    }

    return "$ret$t</trees>\n";
  }

  public function export_one($tree_id, $tab = 0)
  {
    $t = tabs($tab);

    $ret = "$t<tree>\n";

    $name = xmlspecialchars($this->tree_model->get_name($tree_id));
    $ret .= "$t\t<name>$name</name>\n";

    $tax_xml = $this->__export_tax($tree_id, null, $tab + 2);
    $ret .= "$t\t<nodes>\n$tax_xml$t\t</nodes>\n";

    return "$ret$t</tree>\n";
  }

  private function __export_tax($tree, $parent, $count = 2)
  {
    $childs = $this->tax_model->get_taxonomy_children($parent, $tree);

    $ret = '';
    $next_count = $count + 1;

    foreach($childs as &$child) {
      $ret .= tabs($count) . "<taxonomy>\n";
      $ret .= tabs($next_count) . "<name>" . xmlspecialchars($child['name']) . "</name>\n";

      $rank = $child['rank_name'];
      if($rank) {
        $ret .= tabs($next_count) . "<rank>" . xmlspecialchars($rank) . "</rank>\n";
      }

      $ret .= $this->__export_tax($tree, $child['id'], $next_count);
      $ret .= tabs($count) . "</taxonomy>\n"; 
    }

    return $ret;
  }
}