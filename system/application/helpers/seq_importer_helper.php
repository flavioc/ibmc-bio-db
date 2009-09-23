<?php

function import_sequence_file($controller, $file)
{
  if(file_extension($file) == 'xml') {
    $controller->load->library('SequenceImporter');
    return $controller->sequenceimporter->import_xml($file);
  } else {
    return import_fasta_file($controller, $file);
  }
}