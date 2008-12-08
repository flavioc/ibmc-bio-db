<?php

class BioModel extends Model
{
  function BioModel() {
    parent::Model();
  }

  function load_model($name)
  {
    $CI =& get_instance();
    $CI->load->model($name, $name, true);
        
    return $CI->$name;
  }
}
