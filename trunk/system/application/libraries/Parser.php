<?php

class Parser
{
  private $controller = null;
  private $tokenizer = null;
  
  function Parser($ctr = null, $str = '')
  {
    $this->controller = $ctr;
    if($ctr) {
      $ctr->load->model('label_model');
      $ctr->load->model('taxonomy_model');
      $ctr->load->model('sequence_model');
      $this->tokenizer = new Tokenizer($str);
    }
  }
  
  public function parse()
  {
    if($this->tokenizer == null) {
      return null;
    }
    
    return $this->expr();
  }
  
  private function expr()
  {
    $operands = array();
    $operator = null;
    $got_unconsumed_operator = false;
    
    while(true) {
      if(!$this->tokenizer->peek()) {
        if($got_unconsumed_operator) {
          throw new Exception("parse error: extra operator $operator");
        }
        break;
      }
      
      $operand = $this->simple_expr();
      if(!$operand) {
        break;
      }
      
      $got_unconsumed_operator = false;
      
      $operands[] = $operand;
      
      $new_operator = $this->tokenizer->peek();
      if(!$new_operator || ($new_operator != 'or' && $new_operator != 'and')) {
        // can be a final )
        break;
      }
      
      $this->tokenizer->get_next(); // consume
      if($operator == null) {
        $operator = $new_operator;
      } else {
        if($operator != $new_operator) {
          throw new Exception("parse error: different operators $operator and $new_operator");
        }
      }
      
      $got_unconsumed_operator = true;
    }
    
    if(empty($operands)) {
      throw new Exception('parse error: no operands');
    }
    
    if(count($operands) == 1) {
      return $operands[0];
    } else {
      return array('oper' => $operator, 'operands' => $operands);
    }
  }
  
  private function simple_expr()
  {
    $top = $this->tokenizer->peek();
    
    if($top == 'not') {
      return $this->not_expr();
    } else if($top == '(') {
      return $this->paren_expr();
    } else {
      return $this->terminal_expr();
    }
  }
  
  private function not_expr()
  {
    $top = $this->tokenizer->get_next();
    
    if($top != 'not') {
      throw new Exception('parse error: must received a not');
    }
    
    $operand = $this->simple_expr();
    
    return array('oper' => 'not', 'operands' => array($operand));
  }
  
  private function paren_expr()
  {
    $top1 = $this->tokenizer->get_next();
    if($top1 != '(') {
      throw new Exception('parse error: missing inital ( parenthesis');
    }
    
    $ret = $this->expr();
    
    $top2 = $this->tokenizer->get_next();
    if($top2 != ')') {
      throw new Exception('parse error: missing final ) parenthesis');
    }
    
    return $ret;
  }
  
  private function terminal_expr()
  {
    $label_name = $this->tokenizer->get_next();
    if(!$label_name) {
      throw new Exception('parser error: missing label name');
    }
    
    $label = $this->controller->label_model->get_by_name($label_name);
    if(!$label) {
      throw new Exception("unknown label $label_name");
    }
    
    // check private label status
    $CI =& get_instance();
    if(!$label['public'] && !$CI->logged_in) {
      throw new Exception("label is private: $label_name");
    }
    $type = $label['type'];
    
    // boolean labels can go without an operator
    $oper = $this->tokenizer->peek();
    if($type == 'bool' && ($oper == null || !$this->__valid_boolean_oper($oper)))
    {
      return array('label' => $label_name, 'type' => $type, 'oper' => 'eq', 'value' => true);
    }
    
    $oper = $this->tokenizer->get_next(); // consume operator
    
    if(label_special_operator($oper)) {
      return array('label' => $label_name, 'type' => $type, 'oper' => $oper);
    }
    
    if($type == 'position') {
      $what_position = $oper;
      if($what_position != 'start' && $what_position != 'length') {
        throw new Exception("parse error: position label $label_name only accepts start/length: $what_position");
      }
      $oper = $this->tokenizer->get_next();
    }
    
    if(!$oper) {
      throw new Exception("parser error: invalid operator to label $label_name");
    }
    
    $oper = $this->__convert_oper($type, $oper);
    if(!$oper) {
      throw new Exception("parse error: could not deduce operator $oper of type $type ($label_name)");
    }
    
    $value = $this->tokenizer->get_next();
    if($value == null) {
      throw new Exception("parse error: invalid value");
    }
    
    switch($type) {
      case 'position':
        $value_data = array('num' => $value, 'type' => $what_position);
        break;
      case 'date':
        if($value == 'today') {
          $value_data = simple_timestamp_string();
        } else {
          $value_data = $value;
        }
        break;
      case 'bool':
        $value_data = ($value == 'true');
        break;
      default:
        $value_data = $value;
    }
    
    return array('label' => $label_name, 'type' => $type, 'oper' => $oper, 'value' => $value_data);
  }
  
  private function __convert_oper($type, $oper)
  {
    switch($type) {
      case 'integer':
      case 'position':
      case 'float':
        switch($oper) {
          case '=': return 'eq';
          case '>': return 'gt';
          case '<': return 'lt';
          case '>=': return 'ge';
          case '<=': return 'le';
        }
        break;
      case 'tax':
        return 'like';
      case 'text':
      case 'url':
      case 'obj':
        switch($oper) {
          case 'equal': return 'eq';
          case 'contains': return 'contains';
          case 'starts': return 'starts';
          case 'ends': return 'ends';
          case 'regexp': return 'regexp';
        }
        break;
      case 'date':
        switch($oper) {
          case 'before': return 'before';
          case 'after': return 'after';
        }
        break;
    }

    switch($oper) {
      case 'is': case '=': case 'eq': case 'equal': return 'eq';
      case 'exists': return 'exists';
    }
    
    return null;
  }
  
  private function __valid_boolean_oper($oper)
  {
    return $this->__convert_oper('bool', $oper) != null;
  }
}