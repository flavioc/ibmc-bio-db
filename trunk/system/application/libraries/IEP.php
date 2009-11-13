<?php

/* Runs the iep EMBOSS utility over a sequence. */

class IEP
{
  private $iep_path = null;
  private $output = null;
  
  function IEP()
  {
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
    if($options['sbegin1'] != null) {
      $command .= ' -sbegin1 ' . $options['sbegin1'];
    }
    if($options['send1'] != null) {
      $command .= ' -send1 ' . $options['send1'];
    }
    if($options['sreverse1'] != null) {
      $command .= ' -sreverse1 ' . parse_yes_r($options['sreverse1']);
    }
    if($options['snucleotide1'] != null) {
      $command .= ' -snucleotide1 ' . parse_yes_r($options['snucleotide1']);
    }
    if($options['sprotein1'] != null) {
      $command .= ' -sprotein1 ' . parse_yes_r($options['sprotein1']);
    }
    $command .= ' -step ' . $options['step'];
    
    $this->output = run_util_over_sequence($sequence, $command);
    
    return true;
  }
  
  public function get_isoelectric_point()
  {
    if(preg_match('/Isoelectric Point = (.*)/', $this->output, $matches)) {
      return (float)($matches[1]);
    } else {
      return null;
    }
  }
}
