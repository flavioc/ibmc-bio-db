<?php

function export_sequences_simple($sequences)
{
  $ret = '';
  
  foreach($sequences as &$seq) {
    $ret .= __get_sequence_simple_header($seq);
    $content = trim($seq['content']);
    $ret .= "\n$content\n";
  }
  
  return $ret;
}

function write_sequences_to_fasta($sequences)
{
  $temp_file = generate_new_file_name();
  $fp = fopen($temp_file, 'w');
  
  fwrite($fp, export_sequences_simple($sequences));
  fclose($fp);
  
  return $temp_file;
}

function export_sequences_xml($sequences, $seq_labels, $author, $what, $tab = 0)
{
  $t = tabs($tab);
  
  $merged_labels = __merge_export_labels($seq_labels);
  $ret = "$t<sequences>\n";
  
  $author = xmlspecialchars($author);
  $ret .= "$t\t<author>$author</author>\n";
  
  $timestamp = xmlspecialchars(timestamp_string());
  $ret .= "$t\t<date>" . $timestamp . "</date>\n";
  
  $ret .= "$t\t<what>$what</what>\n";
  $ret .= __get_export_header_xml($merged_labels, $tab + 1);
  
  for($i = 0; $i < count($sequences); $i++) {
    $sequence = $sequences[$i];
    $labels = $seq_labels[$i];
    
    $ret .= __export_sequence_xml($sequence, $labels, $merged_labels, $tab + 1);
  }
  
  return "$ret$t</sequences>";
}

function __get_export_header_xml($merged_labels, $tab = 1)
{
  $t = tabs($tab);
  $ret = "$t<labels>\n";
  
  foreach($merged_labels as &$label) {
    if(__label_type_is_printable($label['type'])) {
      $name = $label['name'];
      $type = $label['type'];
      $ret .= "$t\t<label type=\"$type\">$name</label>\n";
    }
  }
  
  return "$ret$t</labels>\n";
}

function __export_sequence_xml($sequence, $labels, $merged_labels, $tab = 1)
{
  $t = tabs($tab);
  
  $ret = "$t<sequence>\n";
  
  # name
  $name = xmlspecialchars(trim($sequence['name']));
  $ret .= "$t\t<name>$name</name>\n";
  
  # content
  $content = xmlspecialchars(trim($sequence['content']));
  $ret .= "$t\t<content>$content</content>\n";
  
  # labels
  foreach($merged_labels as &$merged_label) {
    $res_labels = __get_export_labels($merged_label, $labels);
    
    if(count($res_labels) == 0) {
      continue;
    } else {
      foreach($res_labels as &$label) {
        $str = xmlspecialchars(__get_label_export_data($label));

        $name = xmlspecialchars($label['name']);
        $ret .= "$t\t<label name=\"$name\">$str</label>\n";
      }
    }
  }
  
  return "$ret$t</sequence>\n";
}

function export_sequences_fasta($sequences, $seq_labels, $comments = null)
{
  $merged_labels = __merge_export_labels($seq_labels);
  $ret = __get_export_header($merged_labels, $comments) . "\n";

  for($i = 0; $i < count($sequences); $i++) {
    $sequence = $sequences[$i];
    $labels = $seq_labels[$i];

    $ret .= __get_sequence_header($sequence, $labels, $merged_labels);
    $content = trim($sequence['content']);
    $ret .= "\n$content\n";
  }

  return $ret;
}

function __get_export_header($labels, $comments = null)
{
  $ret = '';

  if($comments) {
    $ret .= ";$comments\n";
  }

  $ret .= '#';
  $first_done = false;

  foreach($labels as &$label) {
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
  case 'integer':
  case 'float':
  case 'text':
  case 'tax':
  case 'url':
  case 'bool':
  case 'ref':
  case 'position':
  case 'date':
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
        $ret[] = array('name' => $label['name'],
                  'type' => $label['type'],
                  'multiple' => $label['multiple']);
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

function __get_export_labels($merged_label, $labels)
{
  $ret = array();
  
  foreach($labels as &$label) {
    if($label['name'] == $merged_label['name']) {
      $ret[] = $label;
    }
  }

  return $ret;
}

function __get_sequence_simple_header(&$sequence)
{
  $seq_name = trim($sequence['name']);
  
  return ">$seq_name";
}

function __get_label_export_data($label)
{
  $type = $label['type'];
  $toadd = '';
  switch($type) {
  case 'integer':
    $toadd = $label['int_data'];
    break;
  case 'float':
    $toadd = $label['float_data'];
    break;
  case 'text':
    $toadd = $label['text_data'];
    break;
  case 'position':
    $toadd = $label['position_start'] . ' ' . $label['position_length'];
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
  case 'date':
    $toadd = $label['date_data'];
    break;
  }
  
  return trim(strval($toadd));
}

function __get_sequence_header($sequence, $labels, $merged_labels)
{
  $seq_name = trim($sequence['name']);
  $ret = ">$seq_name|#";

  foreach($merged_labels as &$merged_label) {
    $res_labels = __get_export_labels($merged_label, $labels);
    if(count($res_labels) == 0) {
      $ret .= '|';
    } else if(count($res_labels) == 1) {
      $ret .= __get_label_export_data($res_labels[0]) . '|';
    } else if($merged_label['multiple']) {
      // multiple labels
      $ret .= '[';
      $first = true;
      foreach($res_labels as &$label) {
        $str = __get_label_export_data($label);
        if($first) {
          $first = false;
        } else {
          $ret .= '§';
        }
        
        $ret .= $str;
      }
      $ret .= ']';
    }
  }

  return $ret;
}

function __write_fasta_file_export($content)
{
  $temp_file = generate_new_file_name();
  $fp = fopen($temp_file, 'w');
  
  fwrite($fp, $content);
  fclose($fp);
  
  return $temp_file;
}

function __convert_export($file_fasta, $type)
{
  $new_file = generate_new_file_name();
  $seqret = __find_seqret();
  
  if($seqret) {
    $cmd = "$seqret $file_fasta $type::$new_file";
    shell_exec($cmd);
  }
  
  return $new_file;
}

function export_sequences_others($sequences, $type)
{
  $fasta_file = write_sequences_to_fasta($sequences);

  $other_file = __convert_export($fasta_file, $type);
  unlink($fasta_file);

  $ret = file_get_contents($other_file);
  unlink($other_file);

  return $ret;
}

function __find_seqret()
{
  return find_executable('seqret');
}

?>