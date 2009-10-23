<?php

class SequenceImporter
{
  function SequenceImporter()
  {
    $CI =& get_instance();
    $CI->load->plugin('import_info');
  }
  
  public function import_xml($file)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      return null;
    }

    return $this->import_xml_node($xmlDoc->documentElement);
  }

  public function import_xml_node($top)
  {
    if(!$top || $top->nodeName != 'sequences') {
      return null;
    }

    $labels_node = find_xml_child($top, 'labels');

    $info = new ImportInfo();

    if($labels_node) {
      foreach($labels_node->childNodes as $label) {
        if($label->nodeName != 'label') {
          continue;
        }

        $name = $label->textContent;
        if(!$name) {
          continue;
        }

        $info->add_label($name);
      }
    }

    foreach($top->childNodes as $child) {
      if($child->nodeName != 'sequence') {
        continue;
      }

      $name_node = find_xml_child($child, 'name');
      if(!$name_node) {
        continue;
      }

      $content_node = find_xml_child($child, 'content');

      $name = xmlspecialchars_decode($name_node->textContent);
      if(!$name) {
        continue;
      }

      $content = xmlspecialchars_decode($content_node->textContent);
      if(!$content) {
        continue;
      }

      $info->add_sequence($name, $content);

      foreach($child->childNodes as $label) {
        if($label->nodeName != 'label') {
          continue;
        }

        $label_name = xmlspecialchars_decode($label->getAttribute('name'));
        if(!$label_name) {
          continue;
        }

        $label_param = xmlspecialchars_decode($label->getAttribute('param'));

        $label_value = xmlspecialchars_decode($label->textContent);
        if(!$label_value && $label_value != '0') {
          continue;
        }

        $info->add_sequence_label($name, $label_name, $label_value, $label_param);
      }
    }

    return $info;
  }
  
  private function __is_sequence_start($line)
  {
    return $line[0] == '>';
  }

  private function __is_header_sequence($line)
  {
    return $line[0] == '#';
  }

  private function __get_sequence_name($line)
  {
    $vec = split("[>#]", $line);
    
    if(count($vec) != 2) {
      return null;
    }
    
    return $vec[0];
  }

  private function __get_sequence_content($file)
  {
    return read_file_line($file);
  }

  private function __read_header_labels(&$info, &$line)
  {
    $stripped_line = trim($line, " \t\n\r\#");
    $labels_vec = explode("|", $stripped_line);

    foreach($labels_vec as $label_text) {
      $label_vec = explode(':', $label_text);
      $label_name = $label_vec[0];

      $info->add_label($label_name);
    }
  }

  private function __split_label_texts($text)
  {
    $trimmed = trim($text, "[]");

    return explode('§', $trimmed);
  }

  private function __is_multiple_values($text)
  {
    $len = strlen($text);

    return $text[0] == '[' && $text[$len-1] == ']';
  }

  private function __get_sequence_labels($info, $name, $line)
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

      if($this->__is_multiple_values($data)) {
        $parts = $this->__split_label_texts($data);

        foreach($parts as $part) {
          if($part == '') {
            continue;
          }

          $vec = $this->__parse_sequence_label_fasta($part);
          $info->add_sequence_label($name, $label_name, $vec[1], $vec[0]);
        }
      } else {
        $vec = $this->__parse_sequence_label_fasta($data);
        $info->add_sequence_label($name, $label_name, $vec[1], $vec[0]);
      }

      ++$i;
    }
  }

  private function __parse_sequence_label_fasta($name)
  {
    $vec = explode(' -> ', $name);

    if(count($vec) == 2) {
      return $vec;
    } else {
      return array(null, $name);
    }
  }

  public function import_fasta($file)
  {
    $info = new ImportInfo();
    $fp = fopen($file, 'rb');
    $line_cnt = 0;
    $has_header = false;

    while(!feof($fp)) {
      $line = read_file_line($fp);

      $line_cnt++;
      if($line == null) {
        continue;
      }

      if($this->__is_header_sequence($line) && !$has_header) {
        $this->__read_header_labels($info, $line);
        $has_header = true;
      } else if($this->__is_sequence_start($line)) {
        $name = $this->__get_sequence_name($line);
        $content = $this->__get_sequence_content($fp);
        
        if(!$content)
          continue;
        
        if(!$name)
          $name = substr($content, 0, 10);

        $info->add_sequence($name, $content);

        $this->__get_sequence_labels($info, $name, $line);
      }
    }

    return $info; 
  }
  
  public function import_file($file)
  {
    if(file_extension($file) == 'xml') {
      return $this->import_xml($file);
    } else {
      return $this->import_fasta($file);
    }
  }
}