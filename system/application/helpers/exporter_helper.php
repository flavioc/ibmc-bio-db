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

function export_sequences_xml($sequences, $seq_labels, $author, $what)
{
  $merged_labels = __merge_export_labels($seq_labels);
  $ret = "<sequences>\n";
  $ret .= "\t<author>$author</author>\n";
  $ret .= "\t<date>" . timestamp_string() . "</date>\n";
  
  $what = xmlspecialchars($what);
  $ret .= "\t<what>$what</what>\n";
  $ret .= __get_export_header_xml($merged_labels);
  
  for($i = 0; $i < count($sequences); $i++) {
    $sequence = $sequences[$i];
    $labels = $seq_labels[$i];
    
    $ret .= __export_sequence_xml($sequence, $labels, $merged_labels);
  }
  
  return "$ret</sequences>";
}

function __get_export_header_xml($merged_labels)
{
  $ret = "\t<labels>\n";
  
  foreach($merged_labels as &$label) {
    if(__label_type_is_printable($label['type'])) {
      $name = $label['name'];
      $type = $label['type'];
      $ret .= "\t\t<label type=\"$type\">$name</label>\n";
    }
  }
  
  return "$ret\t</labels>\n";
}

function __export_sequence_xml($sequence, $labels, $merged_labels)
{
  $ret = "\t<sequence>\n";
  
  # name
  $name = trim($sequence['name']);
  $ret .= "\t\t<name>$name</name>\n";
  
  # content
  $content = trim($sequence['content']);
  $ret .= "\t\t<content>$content</content>\n";
  
  # labels
  foreach($merged_labels as &$merged_label) {
    $label = __get_export_label($merged_label, $labels);
    if(!$label) {
      continue;
    }

    $str = __get_label_export_data($label);

    $name = $label['name'];
    $ret .= "\t\t<label name=\"$name\">$str</label>\n";
  }
  
  return "$ret\t</sequence>\n";
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
  case 'integer':
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

  foreach($merged_labels as $merged_label) {
    $label = __get_export_label($merged_label, $labels);
    $str = __get_label_export_data($label);
    $ret .= "$str|";
  }

  return $ret;
}

function generate_new_file_name_export()
{
  return tempnam(sys_get_temp_dir(), 'bio');
}

function __write_fasta_file_export($content)
{
  $temp_file = generate_new_file_name_export();
  $fp = fopen($temp_file, 'w');
  
  fwrite($fp, $content);
  fclose($fp);
  
  return $temp_file;
}

function __convert_export($file_fasta, $type)
{
  $new_file = generate_new_file_name_export();
  $seqret = __find_seqret();
  
  if($seqret) {
    $cmd = "$seqret $file_fasta $type::$new_file";
    shell_exec($cmd);
  }
  
  return $new_file;
}

function export_sequences_others($sequences, $type)
{
  $fasta_content = export_sequences_simple($sequences);
  $fasta_file = __write_fasta_file_export($fasta_content);

  $other_file = __convert_export($fasta_file, $type);
  unlink($fasta_file);

  $ret = file_get_contents($other_file);
  unlink($other_file);

  return $ret;
}

function __find_seqret()
{
  $normal_paths = array('/bin', '/usr/bin', '/usr/local/bin', '/opt/local/bin');
  $path = '';
  
  foreach($normal_paths as $dir) {
    $full_path = "$dir/seqret";
    if(is_executable($full_path)) {
      return $full_path;
    }
  }
  
  // seqret has not been found, use find
  $output = shell_exec("find / -name seqret");
  foreach(explode("\n", $output) as $line) {
    $line = trim($line);
    if(is_executable($line)) {
      return $line;
    }
  }
  
  return null;
}

?>