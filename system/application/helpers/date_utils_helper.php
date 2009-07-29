<?php

function convert_html_date_to_sql($date)
{
  $vec = explode('-', $date);

  if(count($vec) != 3) {
    return null;
  }
  
  foreach($vec as $num) {
    if(!is_numeric($num)) {
      return null;
    }
  }

  $day = $vec[0];
  $month = $vec[1];
  $year = $vec[2];

  return "$year-$month-$day";
}

function convert_sql_date_to_html($date)
{
  $vec = explode('-', $date);

  if(count($vec) != 3) {
    return null;
  }

  $year = $vec[0];
  $month = $vec[1];
  $day = $vec[2];

  return $day . '-' . $month . '-' . $year;
}

function timestamp_string()
{
  return date('l jS F Y h:i:s A');
}