<?php

class Patmatmotifs
{
  private $path = null;
  private $output = null;
  
  function Patmatmotifs()
  {
    $this->path = find_executable('patmatmotifs');
		
		if(!$this->path) {
		  throw new Exception('Could not find patmatmotifs');
		}
  }
  
  public function run_seq($sequence, $coptions = array())
  {
    $options = array('prune' => true, 'sbegin1' => null, 'send1' => null, 'sreverse1' => null,
                      'snucleotide1' => null, 'sprotein1' => null);
    $options = array_merge($options, $coptions);
    
    $command = $this->path;
    $command .= ' -prune ' . parse_yes_r($options['prune']);
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
    
    $this->output = run_util_over_sequence($sequence, $command);
    
    return true;
  }
}