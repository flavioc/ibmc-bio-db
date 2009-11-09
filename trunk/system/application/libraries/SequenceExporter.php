<?php

class SequenceExporter
{
  function SequenceExporter()
  {
  }
  
  public function export_simple_fasta($sequences)
  {
    $ret = '';

    foreach($sequences as &$seq) {
      $ret .= $this->__get_simple_header($seq);
      $content = trim($seq['content']);
      $ret .= "\n$content\n";
    }

    return $ret;
  }
  
  private function __get_simple_header(&$sequence)
  {
    $seq_name = trim($sequence['name']);

    return ">$seq_name";
  }
  
  function export_xml($sequences, $seq_labels, $author, $what, $tab = 0)
  {
    $t = tabs($tab);

    $merged_labels = $this->__merge_export_labels($seq_labels);
    $ret = "$t<sequences>\n";

    $author = xmlspecialchars($author);
    $ret .= "$t\t<author>$author</author>\n";

    $timestamp = xmlspecialchars(timestamp_string());
    $ret .= "$t\t<date>" . $timestamp . "</date>\n";

    $ret .= "$t\t<what>$what</what>\n";
    $ret .= $this->__get_export_header_xml($merged_labels, $tab + 1);

    for($i = 0; $i < count($sequences); $i++) {
      $sequence = $sequences[$i];
      $labels = $seq_labels[$i];

      $ret .= $this->__export_sequence_xml($sequence, $labels, $merged_labels, $tab + 1);
    }

    return "$ret$t</sequences>";
  }
  
  private function __has_export_label($all, $label)
  {
    foreach($all as $thislabel) {
      if($thislabel['name'] == $label['name']) {
        return true;
      }
    }

    return false;
  }
  
  private function __merge_export_labels($seq_labels)
  {
    $ret = array();

    foreach($seq_labels as $labels) {
      foreach($labels as $label) {
        if(!label_type_is_printable($label['type'])) {
          continue;
        }
        if(!$this->__has_export_label($ret, $label)) {
          $ret[] = array('name' => $label['name'],
                    'type' => $label['type'],
                    'multiple' => $label['multiple']);
        }
      }
    }

    return $ret;
  }
  
  private function __get_export_header_xml($merged_labels, $tab = 1)
  {
    $t = tabs($tab);
    $ret = "$t<labels>\n";

    foreach($merged_labels as &$label) {
      if(label_type_is_printable($label['type'])) {
        $name = $label['name'];
        $ret .= "$t\t<label>$name</label>\n";
      }
    }

    return "$ret$t</labels>\n";
  }
  
  private function __export_sequence_xml($sequence, $labels, $merged_labels, $tab = 1)
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
      $res_labels = $this->__get_export_labels($merged_label, $labels);

      if(count($res_labels) == 0) {
        continue;
      } else {
        foreach($res_labels as &$label) {
          $str = xmlspecialchars($this->__get_label_export_data($label));

          $name = xmlspecialchars($label['name']);
          $ret .= "$t\t<label name=\"$name\"";

          $param = $label['param'];

          if($param) {
            $ret .= " param=\"$param\"";
          }
          $ret .= ">$str</label>\n";
        }
      }
    }

    return "$ret$t</sequence>\n";
  }
  
  private function __get_export_labels($merged_label, $labels)
  {
    $ret = array();

    foreach($labels as &$label) {
      if($label['name'] == $merged_label['name']) {
        $ret[] = $label;
      }
    }

    return $ret;
  }
  
  private function __get_label_export_data($label)
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
    case 'obj':
      $toadd = $label['obj_data'];
      break;
    }

    return trim(strval($toadd));
  }
  
  public function export_fasta($sequences, $seq_labels, $comments = null)
  {
    $merged_labels = $this->__merge_export_labels($seq_labels);
    $ret = $this->__get_export_header($merged_labels, $comments) . "\n";

    for($i = 0; $i < count($sequences); $i++) {
      $sequence = $sequences[$i];
      $labels = $seq_labels[$i];

      $ret .= $this->__get_sequence_header($sequence, $labels, $merged_labels);
      $content = trim($sequence['content']);
      $ret .= "\n$content\n";
    }

    return $ret;
  }
  
  private function __get_export_header($labels, $comments = null)
  {
    $ret = '';

    if($comments) {
      $ret .= ";$comments\n";
    }

    $ret .= '#name';
    $first_done = false;

    foreach($labels as &$label) {
      if(label_type_is_printable($label['type'])) {
        $ret .= '|';

        $name = $label['name'];
        $ret .= $name;

        $first_done = true;
      }
    }

    return $ret;
  }
  
  private function __get_sequence_header($sequence, $labels, $merged_labels)
  {
    $seq_name = trim($sequence['name']);
    $ret = ">$seq_name|";

    foreach($merged_labels as &$merged_label) {
      $res_labels = $this->__get_export_labels($merged_label, $labels);
      if(count($res_labels) == 0) {
        $ret .= '|';
      } else if(count($res_labels) == 1) {
        $ret .= $this->__get_export_label_fasta_name($res_labels[0]) . '|';
      } else if($merged_label['multiple']) {
        // multiple labels
        $ret .= '[';
        $first = true;
        foreach($res_labels as &$label) {
          $str = $this->__get_export_label_fasta_name($label);
          if($first) {
            $first = false;
          } else {
            $ret .= '§';
          }

          $ret .= $str;
        }
        $ret .= ']|';
      }
    }

    return trim($ret, '|');
  }
  
  private function __get_export_label_fasta_name($label)
  {
    $toadd = $this->__get_label_export_data($label);

    $param = $label['param'];
    if($param) {
      $toadd = $param . ' -> ' . $toadd;
    }

    return $toadd;
  }
  
  public function export_others($sequences, $type)
  {
    if($type == 'simple-fasta')
      return $this->export_simple_fasta($sequences);
    
    $fasta_file = $this->write_sequences_to_fasta($sequences);

    $other_file = $this->__convert_export($fasta_file, $type);
    unlink($fasta_file);

    $ret = file_get_contents($other_file);
    unlink($other_file);

    return $ret;
  }
  
  public function write_sequences_to_fasta($sequences)
  {
    $temp_file = generate_new_file_name();
    $fp = fopen($temp_file, 'w');

    fwrite($fp, $this->export_simple_fasta($sequences));
    fclose($fp);

    return $temp_file;
  }

  private function __convert_export($file_fasta, $type)
  {
    $new_file = generate_new_file_name();
    $seqret = find_executable('seqret');

    if($seqret) {
      $cmd = "$seqret $file_fasta $type::$new_file";
      shell_exec($cmd);
    }

    return $new_file;
  }
}