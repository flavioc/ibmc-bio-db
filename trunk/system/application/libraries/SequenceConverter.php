<?php

class SequenceConverter
{
  function SequenceConverter()
  {
  }
  
  public function write_simple_fasta($sequences)
  {
    $CI =& get_instance();
    $CI->load->library('SequenceExporter');
    
    $str = $CI->sequenceexporter->export_simple_fasta($sequences);
    
    return write_file_export($str);
  }
  
  public function convert_dna_sequences($sequences)
  {
    $fasta = write_simple_fasta($sequences);
    
    $protein = $this->convert_dna_fasta($fasta);
    
    unlink($fasta);
      
    return $protein;
  }
  
  public function convert_dna_fasta($fasta)
  {
    $transeq = find_executable('transeq');
    if(!$transeq)
      return null;
    
    $protein = generate_new_file_name();
    
    exec("$transeq $fasta $protein", $cmdoutput, $ret);
    
    if($ret)
      throw new Exception("Error executing transeq");  
    
    return $protein;
  }
  
  public function convert_dna_sequence($name, $content)
  {
    $data = array(array('name' => $name, 'content' => $content));
    
    return $this->convert_dna_sequences($data);
  }
}