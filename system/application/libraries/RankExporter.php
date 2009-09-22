<?php

class RankExporter
{
  function RankExporter()
  {
    
  }
  
  function export_group($ranks)
  {
    $ret = "<ranks>\n";
    foreach($ranks as &$rank) {
      $ret .= $this->__export_rank($rank);
    }
    return "$ret</ranks>";
  }

  private function __export_rank(&$rank)
  {
    $ret = "\t<rank>\n";
    $ret .= "\t\t<name>" . $rank['rank_name'] . "</name>\n";
    $ret .= "\t\t<parent>" . $rank['rank_parent_name'] . "</parent>\n";
    return "$ret\t</rank>\n";
  }
}