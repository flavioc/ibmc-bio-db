<?php

function sql_limit($start, $size)
{
  if($start == null) {
    $start = '0';
  }
  
  if(!is_numeric($start) || !is_numeric($size)) {
    return '';
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

function sql_oper($oper)
{
  switch($oper) {
  case 'eq': return '=';
  case 'gt': return '>';
  case 'lt': return '<';
  case 'ge': return '>=';
  case 'le': return '<=';
  default: return '';
  }
}
