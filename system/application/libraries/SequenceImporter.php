<?php

class SequenceImporter
{
  function SequenceImporter()
  {
    $CI =& get_instance();
    $CI->load->plugin('import_info');
  }
  
  public function import_xml($file, &$event_data = null, $event_component = null)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      return null;
    }

    return $this->import_xml_node($xmlDoc->documentElement, $event_data, $event_component);
  }

  public function import_xml_node($top, &$event_data = null, $event_component = null)
  {
    if(!$top || $top->nodeName != 'sequences') {
      return null;
    }

    $labels_node = find_xml_child($top, 'labels');

    $info = new ImportInfo($event_data, $event_component);

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
        $info->add_empty_sequence($name);
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

  private function __get_sequence_name($line, $has_header, $name_pos)
  {
    if(!$has_header) {
      $vec = explode("|", trim($line, " \n\r\t>"));
      
      if(count($vec) > 0) {
        return $vec[0];
      }
      
      return null;
    }
    
    // has header
    
    if(!is_numeric($name_pos)) {
      return null;
    }
    
    $vec = $this->__get_label_vector_seq($line);
    
    if(count($vec) <= $name_pos) {
      return null;
    }
    
    return $vec[$name_pos];
  }

  private function __get_sequence_content($reader)
  {
    $ret = '';
    while(!$reader->ends()) {
      $new_line = $reader->get_line();
      
      if(!$new_line)
        continue;
      
      if($this->__is_sequence_start($new_line)) {
        $reader->unread_line($new_line);
        return $ret;
      } else {
        $ret .= $new_line;
      }
    }
    
    return $ret;
  }
  
  private function __get_label_vector(&$line)
  {
    return explode("|", trim($line, " \t\n\r\#\|"));
  }
  
  private function __get_label_vector_seq(&$line)
  {
    return explode("|", trim($line, " \t\n\r>"));
  }

  private function __read_header_labels(&$info, &$line, &$pos_name)
  {
    $labels_vec = $this->__get_label_vector($line);

    $i = 0;
    
    foreach($labels_vec as $label_text) {
      $label_vec = explode(':', $label_text);
      $label_name = $label_vec[0];
      
      if($label_name && $label_name != '') {
        if($label_name == 'name') {
          $pos_name = $i;
        }
        
        $info->add_label($label_name);
        ++$i;
      }
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

    if($len == 0)
      return false;
      
    return $text[0] == '[' && $text[$len-1] == ']';
  }

  private function __get_sequence_labels($name, $info, $line)
  {
    $label_data = $this->__get_label_vector_seq($line);

    $total_data = count($label_data);
    $i = 0;
    
    foreach($info->get_labels() as $label_name) {
      if($i >= $total_data) {
        $info->add_sequence_label($name, $label_name, '');
        continue;
      }
    
      ++$i;
      
      if($label_name == 'name')
        continue;
      
      $data = $label_data[$i-1];

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

  public function import_fasta($file, &$event_data = null, $event_component = null)
  {
    $info = new ImportInfo($event_data, $event_component);
    $has_header = false;
    
    $CI =& get_instance();
    $CI->load->plugin('line_reader');

    $name_pos = 'undefined';
    $reader = new LineReader($file);
    
    while(!$reader->ends()) {
      $line = $reader->get_line();

      if (!$line)
        continue;

      if(!$has_header && $this->__is_header_sequence($line)) {
        $this->__read_header_labels($info, $line, $name_pos);
        $has_header = true;
      } else if($this->__is_sequence_start($line)) {
        $name = $this->__get_sequence_name($line, $has_header, $name_pos);
        $content = $this->__get_sequence_content($reader);
        
        if(!$content) {
          $info->add_empty_sequence($line);
          continue;
        }
        
        if(!$name)
          $name = substr($content, 0, 10);

        $info->add_sequence($name, $content);

        if($has_header)
          $this->__get_sequence_labels($name, $info, $line);
      }
    }

    return $info; 
  }
  
  public function import_file($file, &$event_data = null, $event_component = null)
  {
    if(file_extension($file) == 'xml') {
      return $this->import_xml($file, $event_data, $event_component);
    } else {
      return $this->import_fasta($file, $event_data, $event_component);
    }
  }
}