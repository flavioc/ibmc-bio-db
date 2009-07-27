<?php

class Admin extends BioController
{
  function Admin() {
    parent::BioController();
  }
  
  function drop_database()
  {
    if(!($this->logged_in && $this->is_admin)) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->assign('title', 'Database reset');
    
    $this->smarty->view('admin/drop_database');
  }
  
  function drop2()
  {
    if(!($this->logged_in && $this->is_admin)) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->assign('title', 'WARNING: Database reset');
    
    $this->smarty->view('admin/drop2');
  }
  
  function do_drop()
  {
    if(!($this->logged_in && $this->is_admin)) {
      return $this->invalid_permission_admin();
    }
    
    $this->load->model('taxonomy_tree_model');
    $this->load->model('taxonomy_rank_model');
    $this->load->model('label_model');
    $this->load->model('sequence_model');
    
    $this->taxonomy_tree_model->delete_all_custom();
    $this->taxonomy_rank_model->delete_all_custom();
    $this->label_model->delete_all_custom();
    $this->sequence_model->delete_all();
    
    $this->smarty->assign('title', 'Database reset');
    $this->smarty->view('admin/do_drop');
  }
}
?>