<?php

function label_special_purpose($name)
{
  return in_array($name, array('name', 'content', 'creation_user', 'update_user', 'creation_date', 'update_date'));
}

function label_special_operator($oper)
{
  return $oper == 'exists' || $oper == 'notexists';
}

function label_compound_oper($oper)
{
  return $oper == 'or' || $oper == 'and' || $oper == 'not';
}
