<?php

class Tokenizer
{
  private $str = null;
  private $total = 0;
  private $queue = array();
  private $current_pos = 0;
  private $the_end = false;
  
  function Tokenizer($mystr)
  {
    $this->str = trim($mystr);
    $this->total = strlen($this->str);
  }
  
  private function __is_whitespace($val)
  {
    return in_array($val, array(' ', '\t', '\n', '\r'));
  }
  
  private function __is_string_delimiter($val)
  {
    return in_array($val, array("'", '"'));
  }
  
  private function __is_special_char($val)
  {
    return in_array($val, array('(', ')'));
  }
  
  private function __really_the_end()
  {
    return empty($this->queue) && $this->the_end;
  }
  
  private function __fetch_ahead()
  {
    if(!$this->the_end) {
      $tok = $this->__fetch_next();
      array_push($this->queue, $tok);
    }
  }
  
  public function get_next()
  {
    if($this->__really_the_end()) {
      return null;
    }
    
    $this->__fetch_ahead();
    
    if(!empty($this->queue)) {
      $ret = array_shift($this->queue);
      //echo "consuming: $ret";
      return $ret;
    }
    
    return null;
  }
  
  public function peek()
  {
    if($this->__really_the_end()) {
      return null;
    }
    
    $this->__fetch_ahead();
    
    if(!empty($this->queue)) {
      return $this->queue[0];
    }
    
    return null;
  }
  
  private function __fetch_next()
  {
    if($this->the_end) {
      return null;
    }
    
    $inside_string = false;
    $inside_token = false;
    $build_token = "";
    
    while($this->current_pos < $this->total) {
      $char = $this->str[$this->current_pos];
      ++$this->current_pos;
      
      $white = $this->__is_whitespace($char);
      
      if($white && !$inside_token && !$inside_string) {
      } elseif($white && $inside_token) {
        return $build_token;
      } elseif($white && $inside_string) {
        $build_token = "$build_token$char";
      } elseif($this->__is_special_char($char) && $inside_token) {
        --$this->current_pos;
        return $build_token;
      } elseif($this->__is_special_char($char) && !$inside_string) {
        // new token
        return $char;
      } else {
        if($this->__is_string_delimiter($char)) {
          if($inside_token) {
            // shift back
            --$this->current_pos;
            return $build_token;
          } elseif($inside_string) {
            return $build_token;
          } else {
            $inside_string = true;
          }
        } else {
          if($inside_string || $inside_token) {
            $build_token = "$build_token$char";
          } else {
            $inside_token = true;
            $build_token = "$build_token$char";
          }
        }
      }
    }
    
    if($inside_token) {
      $this->the_end = true;
      return $build_token;
    } elseif($inside_string) {
      return null;
    } else {
      return null;
    }
  }
}