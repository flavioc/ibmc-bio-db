<?php

function parse_id($str)
{
  $id_vec = explode('_', $str);

  if(count($id_vec) != 2) {
    return null;
  }

  $id = intval($id_vec[1]);

  return $id;
}

function build_ok_id($id)
{
  return "ok_$id";
}

function build_ok()
{
  return "ok";
}

function parse_yes($yes)
{
  return $yes == 'yes' ? TRUE : FALSE;
}
