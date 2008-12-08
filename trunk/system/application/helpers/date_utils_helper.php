<?php

function convert_html_date_to_sql($date)
{
  $vec = explode('-', $date);

  if(count($vec) != 3) {
    return null;
  }

  $day = $vec[0];
  $month = $vec[1];
  $year = $vec[2];

  return $year . '-' . $day . '-' . $month;
}

function convert_sql_date_to_html($date)
{
  $vec = explode('-', $date);

  if(count($vec) != 3) {
    return null;
  }

  $year = $vec[0];
  $day = $vec[1];
  $month = $vec[2];

  return $day . '-' . $month . '-' . $year;
}

