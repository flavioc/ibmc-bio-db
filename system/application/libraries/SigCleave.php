<?php

class SigCleave
{
  private $sigcleave_path = null;
  private $output = null;
  
  function SigCleave()
  {
    $this->sigcleave_path = find_executable('sigcleave');
		
		if(!$this->sigcleave_path) {
		  throw new Exception('Could not find sigcleave');
		}
  }
  
  public function run_seq($sequence, $coptions = array())
  {
    $options = array('minweight' => '3.5', 'sbegin1' => null, 'send1' => null, 'sreverse1' => null,
                      'snucleotide1' => null, 'sprotein1' => null);
    $options = array_merge($options, $coptions);
    
    $command = $this->sigcleave_path;
    $command .= ' -minweight ' . $options['minweight'];
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
  
  public function get_number_signal_cleavage_sites()
  {
    if(preg_match('/HitCount: (.*)/', $this->output, $matches)) {
      return (int)($matches[1]);
    } else {
      return null;
    }
  }
  
  private function __get_single_regex()
  {
    return '/Score (.*) length (.*) at residues (.*)->(.*)/';
  }
  
  public function get_sigcleave_positions()
  {
    if(preg_match_all($this->__get_single_regex(), $this->output, $matches)) {
      $start = $matches[3];
      $end = $matches[4];
      
      $total = count($start);
      
      $ret = array();
      for($i = 0; $i < $total; ++$i) {
        $start_val = intval($start[$i]);
        $end_val = intval($end[$i]);
        
        $ret[] = array($start_val, $end_val - $start_val);
      }
      
      return $ret;
    } else {
      return null;
    }
  }
  
  public function get_sigcleave_scores()
  {
    if(preg_match_all($this->__get_single_regex(), $this->output, $matches)) {
      return $matches[1];
    } else {
      return null;
    }
  }
}