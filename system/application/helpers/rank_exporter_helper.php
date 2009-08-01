<?php

function export_ranks_xml($ranks)
{
  $ret = "<ranks>\n";
  foreach($ranks as &$rank) {
    $ret .= __export_rank_xml($rank);
  }
  return "$ret</ranks>";
}

function __export_rank_xml(&$rank)
{
  $ret = "\t<rank>\n";
  $ret .= "\t\t<name>" . $rank['rank_name'] . "</name>\n";
  $ret .= "\t\t<parent>" . $rank['rank_parent_name'] . "</parent>\n";
  return "$ret\t</rank>\n";
}