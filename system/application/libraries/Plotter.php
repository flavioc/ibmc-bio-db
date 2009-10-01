<?php

class Plotter
{
  private $label_sequence_model = null;
  private $label_model = null;
  private $search_model = null;
  private $result = null;
  
  function Plotter()
  {
    $this->label_model = load_ci_model('label_model');
    $this->label_sequence_model = load_ci_model('label_sequence_model');
    $this->search_model = load_ci_model('search_model');
  }
  
  // type should be one of these: max, min, avg
  public function make_distribution($search, $transform, $label_id, $type = 'avg', $param = null)
  {
    $label = $this->label_model->get($label_id);
    $label_type = $label['type'];
    
    switch($label_type) {
      case 'float':
      case 'integer':
        $this->result = $this->search_model->get_numeral_search_distribution($search, $label_id,
          array('transform' => $transform, 'distr' => $type, 'label_type' => $label_type,
                'param' => $param));
        break;
      case 'bool':
      case 'text':
      case 'date':
      case 'url':
      case 'ref':
      case 'obj':
      case 'tax':
      case 'position':
        $this->result = $this->search_model->get_other_search_distribution($search, $label_id,
          array('transform' => $transform, 'label_type' => $label_type, 'param' => $param));
        break;
    }
    
    foreach($this->result as &$data) {
      $what = $data['distr'];
      
      if(is_numeric($what)) {
        $data['distr'] = (float)$what;
      }
    }
    
    return true;
  }
  
  public function get_js_data()
  {
    $ret = '{';
    
    foreach($this->result as &$data) {
      $total = (float)$data['total'];
      $what = $data['distr'];
      
      if(strlen($ret) > 1) {
        $ret .= ', ';
      }
      
      $ret .= "'$what': $total";
    }
    
    return "$ret}";
  }
  
  public function get_total()
  {
    $ret = 0;
    
    foreach($this->result as &$data) {
      $ret += $data['total'];
    }
    
    return $ret;
  }
  
  public function get_number_classes()
  {
    return count($this->result);
  }
  
  public function get_minimal_class()
  {
    $min = 'undefined';
    
    foreach($this->result as &$data) {
      $class = $data['distr'];
      
      if($min == 'undefined')
        $min = $class;
      elseif($min > $class)
        $min = $class;
    }
    
    return $min;
  }
  
  public function get_maximal_class()
  {
    $max = 'undefined';
    
    foreach($this->result as &$data) {
      $class = $data['distr'];
      
      if($max == 'undefined')
        $max = $class;
      elseif($max < $class)
        $max = $class;
    }
    
    return $max;
  }
  
  public function get_average()
  {
    $total = (float)$this->get_total();
    $ret = 0.0;
    
    foreach($this->result as &$data) {
      $what = $data['distr'];
      $perc = $data['total'] / $total;
      
      $ret += $perc * $what;
    }
    
    return $ret;
  }
  
  public function get_median()
  {
    $total = $this->get_total();
    if($total % 2) { // even
      $even = true;
      $pos = $total / 2 - 1;
      $divide = 1;
    } else {
      $even = false;
      $pos = intval($total / 2);
      $divide = 2;
    }
    
    $ret = 0;
    $current = 0;
    
    foreach($this->result as &$data) {
      $value = $data['total'];
      
      $old_current = $current;
      $current = $old_current + $value;
      
      if($pos <= $current) {
        $ret += $data['distr'];
        
        if(!$even) {
          ++$pos;
          
          if($pos <= $current)
            return $ret;
          else
            $even = true;
        } else {
          return $ret / $divide; 
        }
      }
    }
  }
  
  public function get_mode()
  {
    $max = 'undefined';
    $mode = null;
    
    foreach($this->result as &$data) {
      $total = $data['total'];
      $distr = $data['distr'];
      
      if($max == 'undefined') {
        $max = $total;
        $mode = $distr;
      } elseif($max < $total) {
        $max = $total;
        $mode = $distr;
      }
    }
    
    return $mode;
  }
}