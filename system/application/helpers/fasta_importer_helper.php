<?php

function __is_sequence_start($line)
{
  return $line[0] == '>';
}

function __is_header_sequence($line)
{
  return $line[0] == '#';
}

function __get_sequence_name($line)
{
  $vec = split("[ \|>]+", $line);

  return $vec[1];
}

function __get_sequence_content($file)
{
  return read_file_line($file);
}

function __read_header_labels(&$info, &$line)
{
  $stripped_line = trim($line, " \t\n\r\#");
  $labels_vec = explode("|", $stripped_line);

  foreach($labels_vec as $label_text) {
    $label_vec = explode(':', $label_text);
    if(count($label_vec) != 2) {
      continue;
    }
    $label_name = $label_vec[0];
    $label_type = $label_vec[1];
    
    $info->add_label($label_name, $label_type);
  }
}

function __split_label_texts($text)
{
  $trimmed = trim($text, "[]");
  
  return explode('§', $trimmed);
}

function __is_multiple_values($text)
{
  $len = strlen($text);
  
  return $text[0] == '[' && $text[$len-1] == ']';
}

function __get_sequence_labels($info, $name, $line)
{
  $stripped_line = trim($line);
  $line_vec = explode('#', $stripped_line);

  if(count($line_vec) <= 1) {
    // no labels
    return;
  }

  // get label data
  $labels_text = $line_vec[count($line_vec)-1];
  $label_data = explode('|', $labels_text);

  $total_data = count($label_data);
  $i = 0;
  foreach($info->get_labels() as $label_name) {
    if($i >= $total_data) {
      break;
    }
    $data = $label_data[$i];
    
    if($data == '') {
      continue;
    }
    
    if(__is_multiple_values($data)) {
      $parts = __split_label_texts($data);
      
      foreach($parts as $part) {
        if($part == '') {
          continue;
        }
        
        $info->add_sequence_label($name, $label_name, $part);
      }
    } else {
      $info->add_sequence_label($name, $label_name, $data);
    }
    
    ++$i;
  }
}

function import_fasta_file($controller, $file)
{
  $info = new ImportInfo($controller);
  $fp = fopen($file, 'rb');
  $line_cnt = 0;
  $has_header = false;

  while(!feof($fp)) {
    $line = read_file_line($fp);

    $line_cnt++;
    if($line == null) {
      continue;
    }
    
    if(__is_header_sequence($line) && !$has_header) {
      __read_header_labels($info, $line);
      $has_header = true;
    } else if(__is_sequence_start($line)) {
      $name = __get_sequence_name($line);
      $content = __get_sequence_content($fp);
      
      $info->add_sequence($name, $content);
      
      __get_sequence_labels($info, $name, $line);
    }
  }
   
  return $info->import(); 
}