<?php

function label_special_purpose($name)
{
  return $name == 'name' || $name == 'content';
}

function label_special_operator($oper)
{
  return $oper == 'exists' || $oper == 'notexists';
}

function label_compound_oper($oper)
{
  return $oper == 'or' || $oper == 'and' || $oper == 'not';
}
