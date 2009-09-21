<?php

/* Run epestfind EMBOSS utility over a sequence. */

class Epestfind
{
  private $path = null;
  private $output = null;
  
  function Epestfind()
  {
    $this->path = find_executable('epestfind');
		
		if(!$this->path) {
		  throw new Exception('Could not find epestfind');
		}
  }
  
  public function run_seq($sequence, $coptions = array())
  {
    $options = array('sbegin1' => null, 'send1' => null, 'sreverse1' => null,
                      'snucleotide1' => null, 'sprotein1' => null,
                      'window' => '10', 'order' => '1', 'threshold' => '+5.0',
                      'mono' => false, 'potential' => true, 'poor' => true,
                      'invalid' => false, 'map' => true);
    $options = array_merge($options, $coptions);
    
    $command = $this->path;
    $command .= ' -graph none';
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
    $command .= ' -window ' . $options['window'];
    $command .= ' -order ' . $options['order'];
    $command .= ' -threshold ' . $options['threshold'];
    $command .= ' -mono ' . parse_yes_r($options['mono']);
    $command .= ' -potential ' . parse_yes_r($options['potential']);
    $command .= ' -poor ' . parse_yes_r($options['poor']);
    $command .= ' -invalid ' . parse_yes_r($options['invalid']);
    $command .= ' -map ' . parse_yes_r($options['map']);
    
    $this->output = run_util_over_sequence($sequence, $command);
    
    return true;
  }
  
  public function get_proteolytic_cleavage_sites()
  {
    if(preg_match('/(.*) PEST motifs were identified in/', $this->output, $matches)) {
      return (int)($matches[1]);
    } else {
      return 0;
    }
  }
  
  public function get_pest_positions()
  {
    if(preg_match_all('/amino acids between position (.*) and (.*)/', $this->output, $matches)) {
      $starts = $matches[1];
      $ends = $matches[2];
      
      $total = count($ends);
      
      $ret = array();
      for($i = 0; $i < $total; ++$i) {
        $start_val = intval($starts[$i]);
        $end_val = intval($ends[$i]);
        
        $ret[] = array($start_val, $end_val - $start_val);
      }
      
      return $ret;
    } else {
      return null;
    }
  }
  
  public function get_pest_amino_acids()
  {
    if(preg_match_all('/PEST motif with (.*) amino acids between position/', $this->output, $matches)) {
      return $matches[1];
    } else {
      return null;
    }
  }
  
  public function get_pest_types()
  {
    if(preg_match_all('/(.*) PEST motif with/', $this->output, $matches)) {
      return $matches[1];
    } else {
      return null;
    }
  }
  
  public function get_pest_scores()
  {
    if(preg_match_all('/PEST score: (.*)/', $this->output, $matches)) {
      $ret = array();
      
      foreach($matches[1] as $score) {
        $ret[] = (float)$score;
      }
      
      return $ret;
    } else {
      return null;
    }
  }
  
  public function get_pest_depsts()
  {
    if(preg_match_all('/DEPST: (.*) %/', $this->output, $matches)) {
      return $matches[1];
    } else {
      return null;
    }
  }
  
  public function get_pest_hydrophobicity()
  {
    if(preg_match_all('/Hydrophobicity index: (.*)/', $this->output, $matches)) {
      return $matches[1];
    } else {
      return null;
    }
  }
}