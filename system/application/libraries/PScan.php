<?php

/* This class runs the pscan (EMBOSS) command over a sequence and uses the output information */

class PScan
{
  private $pscan_path = null;
  private $output = null;
  
  function PScan()
  {
    $this->obj 	=& get_instance();
		$this->obj->load->helper('file_utils'); // find_executable and generate_new_file_name
		$this->obj->load->helper('exporter'); // write_sequences_to_fasta
		
		$this->pscan_path = find_executable('pscan');
		
		if(!$this->pscan_path) {
		  throw new Exception('Could not find pscan');
		}
  }
  
  public function run_seq($sequence, $options = array('emin' => 2, 'emax' => 20))
  {
    $fasta = write_sequences_to_fasta(array($sequence));
    
    $emin = $options['emin'];
    $emax = $options['emax'];
    
    $output_file = generate_new_file_name();
    $command = $this->pscan_path . " -emin $emin -emax $emax $fasta $output_file";
    
    exec($command, $this->output, $ret);
    unlink($fasta);
    
    if($ret) {
      unlink($output_file);
      throw new Exception("pscan failed: $ret");  
    }
    
    $this->output = file_get_contents($output_file);
    unlink($output_file);
    
    return true;
  }
}