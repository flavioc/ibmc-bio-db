<?php

function import_sequence_file($controller, $file)
{
  if(file_extension($file) == 'xml') {
    return import_sequences_xml_file($controller, $file);
  } else {
    return import_fasta_file($controller, $file);
  }
}