<?php

function build_data_array($array, $column = 'name')
{
  $ret = array();

  foreach($array as $value) {
    $ret[] = array($column => $value);
  }

  return $ret;
}
