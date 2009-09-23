<?php

function import_sequences_xml_file($controller, $file)
{
  $xmlDoc = new DOMDocument();
  if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
    return null;
  }
  
  return import_sequences_xml_node($xmlDoc->documentElement, $controller);
}

function import_sequences_xml_node($top, $controller)
{
  if(!$top || $top->nodeName != 'sequences') {
    return null;
  }
  
  $labels_node = find_xml_child($top, 'labels');
  if(!$labels_node) {
    return null;
  }
  
  $info = new ImportInfo();
  
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