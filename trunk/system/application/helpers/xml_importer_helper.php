<?php

function import_xml_file($controller, $file)
{
  $xmlDoc = new DOMDocument();
  if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
    return null;
  }
  
  $top = $xmlDoc->documentElement;
  
  if(!$top) {
    return null;
  }
  
  if($top->nodeName != 'sequences') {
    return null;
  }
  
  $labels_node = find_xml_child($top, 'labels');
  if(!$labels_node) {
    return null;
  }
  
  $info = new ImportInfo($controller);
  
  foreach($labels_node->childNodes as $label) {
    if($label->nodeName != 'label') {
      continue;
    }
    $type = $label->getAttribute('type');
    if(!$type) {
      continue;
    }
    
    $name = $label->textContent;
    if(!$name) {
      continue;
    }
    
    $info->add_label($name, $type);
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
    
    $name = $name_node->textContent;
    if(!$name) {
      continue;
    }
    
    $content = $content_node->textContent;
    if(!$content) {
      continue;
    }
    
    $info->add_sequence($name, $content);
    
    foreach($child->childNodes as $label) {
      if($label->nodeName != 'label') {
        continue;
      }
      
      $label_name = $label->getAttribute('name');
      if(!$label_name) {
        continue;
      }
      
      $label_value = $label->textContent;
      if(!$label_value && $label_value != '0') {
        continue;
      }
      
      $info->add_sequence_label($name, $label_name, $label_value);
    }
  }
  
  return $info->import();
}