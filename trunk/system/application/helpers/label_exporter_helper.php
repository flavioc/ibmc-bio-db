<?php

function export_labels_xml(&$labels)
{
  $ret = "<labels>\n";
  foreach($labels as &$label) {
    $ret .= __export_label_xml($label);
  }
  
  return "$ret</labels>\n";
}

function __export_label_xml(&$label)
{
  $ret = "\t<label>\n";
  $ret .= "\t\t<name>" . xmlspecialchars($label['name']) . "</name>\n";
  $ret .= "\t\t<type>" . $label['type'] . "</type>\n";
  $ret .= "\t\t<comment>" . xmlspecialchars($label['comment']) . "</comment>\n";
  $ret .= "\t\t<default>" . $label['default'] . "</default>\n";
  $ret .= "\t\t<must_exist>" . $label['must_exist'] . "</must_exist>\n";
  $ret .= "\t\t<auto_on_creation>" . $label['auto_on_creation'] . "</auto_on_creation>\n";
  $ret .= "\t\t<auto_on_modification>" . $label['auto_on_modification'] . "</auto_on_modification>\n";
  $ret .= "\t\t<code>" . xmlspecialchars($label['code']) . "</code>\n";
  $ret .= "\t\t<valid_code>" . xmlspecialchars($label['valid_code']) . "</valid_code>\n";
  $ret .= "\t\t<deletable>" . $label['deletable'] . "</deletable>\n";
  $ret .= "\t\t<multiple>" . $label['multiple'] . "</multiple>\n";
  $ret .= "\t\t<public>" . $label['public'] . "</public>\n";
  return "$ret\t</label>\n";
}