<?php

function is_dna_letter($letter)
{
  $dna_letters = array('A', 'G', 'T', 'C');

  return in_array($letter, $dna_letters);
}

function is_protein_letter($letter)
{
  $protein_letters = array('A', 'B', 'C', 'D', 'E', 'F',
  'G', 'H', 'I', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S',
  'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

  return in_array($letter, $protein_letters);
}

function sequence_type($seq)
{
  $type = null;

  $array = str_split(strtoupper($seq));

  foreach($array as $char) {
    if($type == null) {
      if(is_dna_letter($char)) {
      } else if(is_protein_letter($char)) {
        $type = 'protein';
      } else {
        return 'unknown';
      }
    } else if($type == 'protein') {
      if(!is_protein_letter($char)) {
        return 'unknown';
      }
    }
  }

  if(!$type) {
    $type = 'dna';
  }

  return $type;
}

define('SEQUENCE_SPACING', 40);

function sequence_split($content)
{
  $size = strlen($content);

  $ret = '';
  for($i = 0; $i < $size; $i = $i + SEQUENCE_SPACING) {
    $ret .= substr($content, $i, SEQUENCE_SPACING) . "\n";
  }

  return $ret;
}

function sequence_join($content)
{
  $vec = explode("\n", $content);

  $ret = "";

  foreach($vec as $el) {
    $ret .= $el;
  }

  return sequence_normalize($ret);
}

function sequence_normalize($content)
{
  return strtoupper(trim($content));
}

function sequence_short_content($content)
{
  return substr($content, 0, SEQUENCE_SPACING);
}

function is_sequence_start($line)
{
  return $line[0] == '>';
}

function get_sequence_name($line)
{
  $vec = split("[ \|>]+", $line);

  return $vec[1];
}

function get_sequence_content($file)
{
  $line = read_file_line($file);

  return sequence_normalize($line);
}

function import_fasta_file($controller, $file)
{
  $ret = array();

  $fp = fopen($file, 'rb');

  while(!feof($fp)) {
    $line = read_file_line($fp);

    if(!$line) {
      continue;
    }

    if(is_sequence_start($line)) {
      $name = get_sequence_name($line);
      $content = get_sequence_content($fp);

      $el = array(
        'name' => $name,
        'content' => $content,
      );

      $has_name = $controller->sequence_model->has_name($name);
      $el['add'] = !$has_name;

      /*
      if($has_name) {
        $controller->sequence_model->delete($controller->sequence_model->get_id_by_name($name));
        $has_name = false;
      }*/

      if($has_name) {
        $el['id'] = $controller->sequence_model->get_id_by_name($name);
        $el['content'] = $controller->sequence_model->get_content($el['id']);
      } else {
        $el['id'] = $controller->sequence_model->add($name, $content);
      }

      // adicionar no resultado
      $ret[] = $el;
    }
  }

  fclose($fp);
  unlink($file);

  return $ret;
}
