<?php

function read_data_file($file, $func, &$data)
{
  $handle = fopen($file, 'r');

  while(!feof($handle)) {
    $line = fgets($handle);
    $vec = explode('|', $line);
    $func($vec, $data);
  }

  fclose($handle);
}

function read_ranks($file)
{
  $ranks = array();

  read_data_file($file, 'read_ranks_helper', $ranks);

  return $ranks;
}

function read_ranks_helper($vec, &$ranks)
{
  if(count($vec) < 3) {
    return;
  }

  $rank = trim($vec[2]);

  if(!in_array($rank, $ranks)) {
    $ranks[] = $rank;
  }
}
