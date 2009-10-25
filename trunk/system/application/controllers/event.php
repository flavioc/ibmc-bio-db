<?php

class Event extends BioController
{
  function Event()
  {
    parent::BioController();
    $this->load->model('event_model');
  }
  
  public function get_event_status($event)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }
    
    $info = $this->event_model->get($event);
    
    $this->smarty->assign('event', $info);
  
    $this->smarty->view_s('event/show');
  }
}