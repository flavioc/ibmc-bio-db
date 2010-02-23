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
    $fasta = $this->write_simple_fasta($sequences);
    
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
  
  private function __get_fasta_header($file)
  {
    $CI =& get_instance();
    $CI->load->plugin('line_reader');

    $reader = new LineReader($file);
    
    $line = $reader->get_line();
    
    if($line[0] == '#')
      return $line;
    return '';
  }
  
  public function convert_dna_protein($dna_file)
  {
    $protein_file = generate_new_file_name();
    
    $header = $this->__get_fasta_header($dna_file);
    
    # remove header line
    $dna_file2 = generate_new_file_name();
    $sed = find_executable("sed");
    $cmd = "$sed -e '/#.*/d' $dna_file > $dna_file2";
    $do_sed = shell_exec($cmd);
    if($do_sed) {
      unlink($dna_file2);
      return null;
    }
    
    $protein_file = $this->convert_dna_fasta($dna_file2);
    unlink($dna_file2);
    
    # put header back
    $new_protein = write_raw_file($header . "\n");
    shell_exec("cat $protein_file >> $new_protein");
    shell_exec("mv $new_protein $protein_file");
    
    # convert protein names
    $cmd = "$sed -e 's/_[0-9]/_p/g' -i '' $protein_file";
    $do_sed = shell_exec($cmd);
    if($do_sed) {
      unlink($protein_file);
      return null;
    }
    
    return $protein_file;
  }
}