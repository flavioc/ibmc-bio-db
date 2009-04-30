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

function is_header_sequence($line)
{
  return $line[0] == '#';
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

function read_header_labels($line, $controller)
{
  $stripped_line = trim($line, " \t\n\#");
  $labels_vec = explode("|", $stripped_line);
  $ret = array();

  foreach($labels_vec as $label_text) {
    $label_vec = explode(":", $label_text);
    if(count($label_vec) != 2) {
      continue;
    }
    $label_name = $label_vec[0];
    $label_type = $label_vec[1];

    if(!$controller->label_model->has_name($label_name)) {
      $ret[] = array('name' => $label_name, 'type' => $label_type, 'status' => 'not_found');
    } else {
      $new_label = $controller->label_model->get_by_name($label_name);

      if($new_label == null) {
        die("SHOULD NOT HAPPEN");
      } else {
        if($new_label['type'] != $label_type) {
          $ret[] = array('name' => $label_name,
                    'type' => $label_type,
                    'status' => 'type_differ',
                    'new_type' => $new_label['type']);
        } else {
          $ret[] = array('name' => $label_name,
                        'type' => $label_type,
                        'status' => 'ok',
                        'data' => $new_label);
        }
      }
    }
  }

  return $ret;
}

function import_update_label_content($id, $label_type, $text, $controller)
{
  $model = $controller->label_sequence_model;
  switch($label_type) {
    case 'integer':
      return $model->edit_integer_label($id, intval($text));
    case 'text':
      return $model->edit_text_label($id, $text);
    case 'position':
      $vec = explode(' ', $text);
      if(count($vec) != 2) {
        return false;
      }
      return $model->edit_position_label($id, intval($vec[0]), intval($vec[1]));
    case 'url':
      return $model->edit_url_label($id, $text);
    case 'bool':
      return $model->edit_bool_label($id, $text);
    case 'ref':
      $row = $controller->sequence_model->get_by_name($text);
      if($row == null) {
        return false;
      }
      return $model->edit_ref_label($id, $row['id']);
    case 'tax':
      $row = $controller->taxonomy_model->get_by_name($text);
      if($row == null) {
        return false;
      }
      return $model->edit_tax_label($id, $row['id']);
  }
  return false;
}
function import_label_content($seq_id, $label_id, $label_type, $text, $controller)
{
  $model = $controller->label_sequence_model;

  switch($label_type) {
    case 'integer':
      return $model->add_integer_label($seq_id, $label_id, intval($text));
    case 'text':
      return $model->add_text_label($seq_id, $label_id, $text);
    case 'position':
      $vec = explode(' ', $text);
      if(count($vec) != 2) {
        return false;
      }
      return $model->add_position_label($seq_id, $label_id, intval($vec[0]), intval($vec[1]));
    case 'ref':
      $row = $controller->sequence_model->get_by_name($text);
      if($row == null) {
        return false;
      }
      return $model->add_ref_label($seq_id, $label_id, $row['id']);
    case 'tax':
      $row = $controller->taxonomy_model->get_by_name($text);
      if($row == null) {
        return false;
      }
      return $model->add_tax_label($seq_id, $label_id, $row['id']);
    case 'url':
      return $model->add_url_label($seq_id, $label_id, $text);
    case 'bool':
      return $model->add_bool_label($seq_id, $label_id, $text);
  }
  return false;
}

function get_import_label_text_natural($text, $type)
{
  switch($type) {
  case 'bool':
    return $text == '0' ? 'No' : 'Yes';
  }

  return $text;
}

function import_labels($el, $labels, $labeldata, $controller)
{
  $ret = array();
  $seq_id = $el['id'];

  for($i = 0; $i < count($labels); ++$i) {
    $label_text = $labeldata[$i];
    $label = $labels[$i];
    $label_name = $label['name'];
    $label_info = $label['data'];
    $label_status = $label['status'];
    $label_id = $label_info['id'];
    $label_type = $label_info['type'];
    $label_text_natural = get_import_label_text_natural($label_text, $label_type);

    $ret[$label_name] = array();
    $label_array =& $ret[$label_name];

    if($label_text == null || $label_text == '') {
      $label_array['status'] = 'Empty / Not inserted';
      continue;
    }

    if($label_status != 'ok') {
      $label_array['status'] = 'Invalid';
      continue;
    }

    $already_there = $controller->label_sequence_model->label_used_up($seq_id, $label_id);
    $editable = $label_info['editable'];
    $multiple = $label_info['multiple'];

    if($already_there && !$editable && !$multiple) {
      $label_array['status'] = 'Already inserted';
    } else if($already_there && $editable && !$multiple) {
      $id = $controller->label_sequence_model->get_label_id($seq_id, $label_id);
      if(import_update_label_content($id, $label_type, $label_text, $controller))
      {
        $label_array['status'] = "Updated value: $label_text_natural";
      } else {
        $label_array['status'] = "Parse error: $label_text";
      }
    } else {
      if($label_info['auto_on_creation']) {
        $label_array['status'] = 'Generated';
        $controller->label_sequence_model->add_auto_label($seq_id, $label_info);
      } else {
        if(import_label_content($seq_id, $label_id, $label_type, $label_text, $controller)) {
          $label_array['status'] = "Value: $label_text_natural";
        } else {
          $label_array['status'] = "Parse error: $label_text";
        }
      }
    }
  }

  return $ret;
}

function get_sequence_labels($line, $labels)
{
  $stripped_line = trim($line, " \n\t");
  $line_vec = explode('#', $stripped_line);

  if(count($line_vec) <= 1) {
    $ret = array();
    foreach($labels as $label) {
      array_push($ret, null);
    }

    return $ret;
  }

  // get last
  $labels_text = $line_vec[count($line_vec)-1];
  $label_vec = explode('|', $labels_text);

  return $label_vec;
}

function import_fasta_file($controller, $file)
{
  $has_header = false;
  $labels = array();
  $sequences = array();

  $fp = fopen($file, 'rb');

  while(!feof($fp)) {
    $line = read_file_line($fp);

    if(!$line) {
      continue;
    }

    if(is_header_sequence($line) && !$has_header) {
      $labels = read_header_labels($line, $controller);
      $has_header = true;
    } else if(is_sequence_start($line)) {
      $name = get_sequence_name($line);
      $labeldata = get_sequence_labels($line, $labels);
      $content = get_sequence_content($fp);

      $sequence = array();
      $el = array(
        'name' => $name,
        'content' => $content,
      );

      $has_name = $controller->sequence_model->has_name($name);

      if($has_name) {
        $controller->sequence_model->delete($controller->sequence_model->get_id_by_name($name));
        $has_name = false;
      }

      if($has_name) {
        $el['id'] = $controller->sequence_model->get_id_by_name($name);
        $el['content'] = $controller->sequence_model->get_content($el['id']);
        $sequence['comment'] = 'Sequence name already exists.';
      } else {
        $el['id'] = $controller->sequence_model->add($name, $content);
      }

      $is_new = !$has_name;
      $sequence['add'] = $is_new;

      if($is_new) {
        $sequence['labels'] = import_labels($el, $labels, $labeldata, $controller);
      }

      $sequence['data'] = $el;

      // adicionar no resultado
      $sequences[] = $sequence;
    }
  }

  fclose($fp);
  //unlink($file);

  return array($sequences, $labels);
}

function export_sequences($sequences, $seq_labels)
{
  $merged_labels = __merge_export_labels($seq_labels);
  $ret = __get_export_header($merged_labels) . "\n";

  for($i = 0; $i < count($sequences); $i++) {
    $sequence = $sequences[$i];
    $labels = $seq_labels[$i];

    $ret .= __get_sequence_header($sequence, $labels, $merged_labels);
    $content = $sequence['content'];
    $ret .= "\n$content";
  }

  return $ret;
}

function __get_export_header($labels)
{
  $ret = '#';
  $first_done = false;

  foreach($labels as $label) {
    if(__label_type_is_printable($label['type'])) {
      if($first_done) {
        $ret .= '|';
      }

      $name = $label['name'];
      $type = $label['type'];
      $ret .= "$name:$type";

      $first_done = true;
    }
  }

  return $ret;
}

function __label_type_is_printable($type)
{
  switch($type) {
  case "integer":
  case "text":
  case "tax":
  case "url":
  case "bool":
  case "ref":
  case "position":
    return true;
  default:
    return false;
  }
}

function __merge_export_labels($seq_labels)
{
  $ret = array();

  foreach($seq_labels as $labels) {
    foreach($labels as $label) {
      if(!__label_type_is_printable($label['type'])) {
        continue;
      }
      if(!__has_export_label($ret, $label)) {
        $ret[] = array('name' => $label['name'], 'type' => $label['type']);
      }
    }
  }

  return $ret;
}

function __has_export_label($all, $label)
{
  foreach($all as $thislabel) {
    if($thislabel['name'] == $label['name']) {
      return true;
    }
  }

  return false;
}

function __get_export_label($merged_label, $labels)
{
  foreach($labels as $label) {
    if($label['name'] == $merged_label['name']) {
      return $label;
    }
  }

  return null;
}

function __get_sequence_header($sequence, $labels, $merged_labels)
{
  $seq_name = $sequence['name'];
  $ret = ">$seq_name|#";
  $first_done = false;

  foreach($merged_labels as $merged_label) {
    $label = __get_export_label($merged_label, $labels);

    $type = $label['type'];
    $toadd = '';
    switch($type) {
    case 'integer':
      $toadd = $label['int_data'];
      break;
    case 'text':
      $toadd = $label['text_data'];
      break;
    case 'position':
      $toadd = $label['position_a_data'] . ' ' . $label['position_b_data'];
      break;
    case 'bool':
      $toadd = $label['bool_data'];
      break;
    case 'url':
      $toadd = $label['url_data'];
      break;
    case 'tax':
      $toadd = $label['taxonomy_name'];
      break;
    case 'ref':
      $toadd = $label['sequence_name'];
      break;
    }

    $ret .= "$toadd|";

    $first_done = true;
  }

  return $ret;
}

