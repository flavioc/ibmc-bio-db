<?php

class Plotter
{
  private $label_sequence_model = null;
  private $label_model = null;
  private $search_model = null;
  
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
        $result = $this->search_model->get_numeral_search_distribution($search, $label_id,
          array('transform' => $transform, 'distr' => $type, 'label_type' => $label_type,
                'param' => $param));
        print_r($result);
        break;
      case 'bool':
      case 'text':
      case 'date':
      case 'url':
      case 'ref':
      case 'obj':
      case 'tax':
      case 'position':
        $result = $this->search_model->get_other_search_distribution($search, $label_id,
          array('transform' => $transform, 'label_type' => $label_type, 'param' => $param));
      
        print_r($result);
        break;
    }
  }
}