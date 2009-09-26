<?php

class RankExporter
{
  function RankExporter()
  {
    
  }
  
  function export_group($ranks, $tab = 0)
  {
    $t = tabs($tab);
    
    $ret = "$t<ranks>\n";
    foreach($ranks as &$rank) {
      $ret .= $this->__export_rank($rank, $tab + 1);
    }
    return "$ret$t</ranks>";
  }

  private function __export_rank(&$rank, $tab = 1)
  {
    $t = tabs($tab);
    
    $ret = "$t<rank>\n";
    $ret .= "$t\t<name>" . $rank['rank_name'] . "</name>\n";
    $ret .= "$t\t<parent>" . $rank['rank_parent_name'] . "</parent>\n";
    
    return "$ret$t</rank>\n";
  }
}