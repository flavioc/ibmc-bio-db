<?php

function form_label_error($what, $for, $data = array())
{
  $data['class'] = 'formerror';

  return form_label($what, $for, $data);
}

function build_error_name($what)
{
  return $what . '_error';
}

function build_initial_name($what)
{
  return 'initial_' . $what;
}

?>
