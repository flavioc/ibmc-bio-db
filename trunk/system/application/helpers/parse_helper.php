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
  return str_replace("\t", "   ", str_replace("\n", "<br />", $text));
}
