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
  
  public function run_seq($sequence, $coptions = array())
  {
    $options = array('emin' => 2, 'emax' => 20);
    $options = array_merge($options, $coptions);
    
    $emin = $options['emin'];
    $emax = $options['emax'];
    
    $command = $this->pscan_path . " -emin $emin -emax $emax";
    
    $this->output = run_util_over_sequence($sequence, $command);
    
    return true;
  }
}