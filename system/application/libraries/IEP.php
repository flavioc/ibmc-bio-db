<?php

/* Runs the iep EMBOSS utility over a sequence. */

class IEP
{
  private $iep_path = null;
  private $output = null;
  
  function IEP()
  {
    $this->obj 	=& get_instance();
		$this->obj->load->helper('file_utils'); // find_executable and generate_new_file_name
		$this->obj->load->helper('exporter'); // write_sequences_to_fasta
		
		$this->iep_path = find_executable('iep');
		
		if(!$this->iep_path) {
		  throw new Exception('Could not find iep');
		}
  }
  
  public function run_seq($sequence, $coptions = array())
  {
    $options = array('amino' => '1', 'termini' => true,
          'lysinemodified' => '0', 'disulphides' => '0',
          'sbegin1' => null, 'send1' => null, 'sreverse1' => null,
          'snucleotide1' => null, 'sprotein1' => null,
          'step' => '0.5');
    
    $options = array_merge($options, $coptions);
    
    $command = $this->iep_path;
    
    $command .= ' -amino ' . $options['amino'];
    $command .= ' -termini ' . parse_yes_r($options['termini']);
    $command .= ' -lysinemodified ' . $options['lysinemodified'];
    $command .= ' -disulphides ' . $options['disulphides'];
    if($options['sbegin1']) {
      $command .= ' -sbegin1 ' . $options['sbegin1'];
    }
    if($options['send1']) {
      $command .= ' -send1 ' . $options['send1'];
    }
    if($options['sreverse1']) {
      $command .= ' -sreverse1 ' . parse_yes_r($options['sreverse1']);
    }
    if($options['snucleotide1']) {
      $command .= ' -snucleotide1 ' . parse_yes_r($options['snucleotide1']);
    }
    if($options['sprotein1']) {
      $command .= ' -sprotein1 ' . parse_yes_r($options['sprotein1']);
    }
    $command .= ' -step ' . $options['step'];
    
    $this->output = run_util_over_sequence($sequence, $command);
    
    return true;
  }
}