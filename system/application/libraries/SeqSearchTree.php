<?php

class SeqSearchTree
{
  function SeqSearchTree()
  {
    
  }
  
  public function get_tree($sequences)
  {
    $operands = array();

    $ids = array();
    foreach($sequences as &$seq) {
      $ids[] = intval($seq['id']);
    }

    sort($ids);

    $current = null;
    $start = null;

    foreach($ids as $id) {
      if($current == null) {
        $current = $id;
        $start = $id;
      } else {
        if($id == ($current + 1)) {
          // subsequent!
          $current = $id;
        } else {
          $this->__push_new_operand($operands, $start, $current);
          $current = $id;
          $start = $id;
        }
      }
    }

    if($current != null) {
      $this->__push_new_operand($operands, $start, $current);
    }

    if(count($operands) == 1) {
      return $operands[0];
    } else {
      return array('oper' => 'or', 'operands' => $operands);
    }
  }

  private function __push_new_operand(&$operands, $start, $end)
  {
    if($start == $end) {
      $operands[] = array('oper' => 'eq', 'label' => 'internal_id', 'type' => 'integer', 'value' => $start);
    } else {
      $first_term = array('oper' => 'ge', 'label' => 'internal_id', 'type' => 'integer', 'value' => $start);
      $second_term = array('oper' => 'le', 'label' => 'internal_id', 'type' => 'integer', 'value' => $end);
      $operands[] = array('oper' => 'and', 'operands' =>
            array($first_term, $second_term));
    }
  }
}