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
