<?php

class BlastLib
{
  public static $expect_values = array(array('id' => 1, 'name' => '0.0001'),
                                               array('id' => 2, 'name' => '0.01'),
                                               array('id' => 3, 'name' => '1'),
                                               array('id' => 4, 'name' => '10'),
                                               array('id' => 5, 'name' => '100'),
                                               array('id' => 6, 'name' => '1000'));
                                               
  public static $matrix_values = array(array('id' => 1, 'name' => 'PAM30'),
                                          array('id' => 2, 'name' => 'PAM70'),
                                          array('id' => 3, 'name' => 'BLOSUM80'),
                                          array('id' => 4, 'name' => 'BLOSUM62'),
                                          array('id' => 5, 'name' => 'BLOSUM45'));
  function BlastLib()
  {
  }
  
  public function lookup_expect_value($id)
  {
    foreach(self::$expect_values as $val) {
      if($val['id'] == $id)
        return $val['name']; 
    }
    
    return '';
  }
  
  public function lookup_matrix_value($id)
  {
    foreach(self::$matrix_values as $val) {
      if($val['id'] == $id)
        return $val['name'];
    }
    
    return '';
  }
}