<?php

function output_autocomplete($words)
{
  foreach($words as $word) {
    echo "$word\n";
  }
}

function output_autocomplete_data($data, $key)
{
  $arr = array();

  foreach($data as $d) {
    $arr[] = $d[$key];
  }

  return output_autocomplete($arr);
}
