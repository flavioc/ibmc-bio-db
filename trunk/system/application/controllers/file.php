<?php

class File extends BioController
{
  function File()
  {
    parent::BioController();
    $this->load->model('file_model');
  }
  
  function get($id)
  {
    $file = $this->file_model->get($id);
    
    if(!$file) {
      return $this->invalid_permission_empty();
    }
    
    $label_id = $file['label_id'];
    
    if($label_id) {
     $this->load->model('label_model');
     
     if(!$this->label_model->is_public($label_id) && !$this->logged_in)
      return $this->invalid_permission_empty();
    }
    
    header("Content-Disposition: attachment; filename=".$file['name']);
    
    echo $file['data'];
  }
  
  function background()
  {
    if($this->file_model->has_background()) {
      $file = $this->file_model->get_background();
      header('Content-Type: image/'.$file['type']);
      header("Content-Disposition: attachment; filename=".$file['name'].'.'.$file['type']);
      echo $file['data'];
    }
  }
}