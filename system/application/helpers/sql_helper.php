<?php

function sql_limit($start, $size)
{
  if($start == null) {
    $start = '0';
  }

  if($size != NULL) {
    return " LIMIT $start, $size ";
  } else {
    return '';
  }
}

function sql_is_nothing($val)
{
  return $val == null || $val == '' || $val == '0';
}
