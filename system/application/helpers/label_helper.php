<?php

function label_special_purpose($name)
{
  return $name == 'name' || $name == 'content';
}

function label_special_operator($oper)
{
  return $oper == 'exists' || $oper == 'notexists';
}
