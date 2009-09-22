<?php

/* This class runs the pscan (EMBOSS) command over a sequence and uses the output information */

class PScan
{
  private $pscan_path = null;
  private $output = null;
  
  function PScan()
  {
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
  
  private function __get_class($class)
  {
    if($class == 4) {
      if(preg_match("/CLASS 4(.*)\z/msU", $this->output, $matches)) {
        return $matches[1];
      }
    } else {
      $next = $class + 1;
      
      if(preg_match("/CLASS $class(.*)CLASS $next/msU", $this->output, $matches)) {
        return $matches[1];
      }
    }
    
    return null;
  }
  
  private function __get_fingerprint_class()
  {
    $ret = array();
    
    for($i = 1; $i <= 4; ++$i) {
      $class = $this->__get_class($i);
    
      if(preg_match_all('/Fingerprint (.*) Elements/', $class, $matches)) {
        foreach($matches[1] as &$fingerprint) {
          $ret[] = array('fingerprint' => $fingerprint, 'class' => $i);
        }
      }
    }
    
    return $ret;
  }
  
  public function get_prints_fingerprints()
  {
    $data = $this->__get_fingerprint_class();
    $ret = array();
    
    foreach($data as &$el) {
      $fingerprint = $el['fingerprint'];
      $class = $el['class'];
      $ret[] = new LabelData($fingerprint, "$class");
    }
    
    return $ret;
  }
  
  public function get_prints_fingerprint_elements()
  {
    $ret = array();
    
    for($i = 1; $i <= 4; ++$i) {
      $class = $this->__get_class($i);
    
      if(preg_match_all('/Fingerprint (.*) Elements (.*)\n/', $class, $matches)) {
        $total = count($matches[1]);
        for($j = 0; $j < $total; ++$j) {
          $fingerprint = $matches[1][$j];
          $ret[] = new LabelData($matches[2][$j], "class$i/$fingerprint");
        }
      }
    }
    
    return $ret;
  }
  
  public function get_prints_fingerprint_accession()
  {
    $ret = array();
    
    for($i = 1; $i <= 4; ++$i) {
      $class = $this->__get_class($i);
    
      if(preg_match_all('/Fingerprint (.*) Elements (.*)Accession number (.*)\n/msU', $class, $matches)) {
        $total = count($matches[1]);
        for($j = 0; $j < $total; ++$j) {
          $fingerprint = $matches[1][$j];
          $ret[] = new LabelData($matches[3][$j], "class$i/$fingerprint");
        }
      }
    }
    
    return $ret;
  }
  
  public function get_prints_fingerprint_thresholds()
  {
    $ret = array();
    $data = $this->__get_fingerprint_class();
    $current_fingerprint = -1;
    
    if(preg_match_all('/Element (.*) Threshold (.*)% Score/', $this->output, $matches)) {
      $elements = $matches[1];
      $thresholds = $matches[2];
      $total = count($elements);
      
      for($i = 0; $i < $total; ++$i) {
        $element = (int)($elements[$i]);
        $threshold = (float)($thresholds[$i]);
        
        if($element == 1) {
          $current_fingerprint++;
        }
        
        $class = $data[$current_fingerprint]['class'];
        $fingerprint = $data[$current_fingerprint]['fingerprint'];
        
        $ret[] = new LabelData($threshold, "class$class/$fingerprint/$element");
      }
      
      return $ret;
    } else {
      return null;
    }
  }
  
  public function get_prints_fingerprint_scores()
  {
    $ret = array();
    $data = $this->__get_fingerprint_class();
    $current_fingerprint = -1;
    
    if(preg_match_all('/Element (.*) Threshold (.*) Score (.*)%/', $this->output, $matches)) {
      $elements = $matches[1];
      $scores = $matches[3];
      $total = count($elements);
      
      for($i = 0; $i < $total; ++$i) {
        $element = (int)($elements[$i]);
        $score = (float)($scores[$i]);
        
        if($element == 1) {
          $current_fingerprint++;
        }
        
        $class = $data[$current_fingerprint]['class'];
        $fingerprint = $data[$current_fingerprint]['fingerprint'];
        
        $ret[] = new LabelData($score, "class$class/$fingerprint/$element");
      }
      
      return $ret;
    } else {
      return null;
    }
  }
  
  public function get_prints_fingerprint_positions()
  {
    $ret = array();
    $data = $this->__get_fingerprint_class();
    $current_fingerprint = -1;
    
    if(preg_match_all('/Element (.*) Threshold (.*)Start position (.*) Length (.*)\n/msU', $this->output, $matches)) {
      $elements = $matches[1];
      $starts = $matches[3];
      $lengths = $matches[4];
      $total = count($elements);
      
      for($i = 0; $i < $total; ++$i) {
        $element = (int)($elements[$i]);
        $start = (int)($starts[$i]);
        $length = (int)($lengths[$i]);
        
        if($element == 1) {
          $current_fingerprint++;
        }
        
        $class = $data[$current_fingerprint]['class'];
        $fingerprint = $data[$current_fingerprint]['fingerprint'];
        
        $ret[] = new LabelData(array($start, $length), "class$class/$fingerprint/$element");
      }
      
      return $ret;
    } else {
      return null;
    }
  }
}