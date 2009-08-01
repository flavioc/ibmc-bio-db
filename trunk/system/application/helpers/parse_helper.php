<?php

function parse_id($str)
{
  $id_vec = explode('_', $str);

  $id = intval($id_vec[count($id_vec)-1]);

  return $id;
}

function parse_yes($yes)
{
  return $yes == 'yes' ? TRUE : FALSE;
}

function newline_tab_html($text)
{
  return ascii_to_entities(str_replace("\t", "   ", str_replace("\n", "<br />", $text)));
}

function xmlspecialchars($text)
{
   return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
}

function xmlspecialchars_decode($text)
{
  return str_replace('&apos;', '&#039;', htmlspecialchars_decode($text, ENT_QUOTES));
}

function find_xml_child($node, $what)
{
  foreach($node->childNodes as $child) {
    if($child->nodeName == $what) {
      return $child;
    }
  }
  
  return null;
}

function parse_boolean_value($val)
{
  if(!$val) {
    return false;
  }
  
  return $val == '1';
}