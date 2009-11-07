<?php

class Delete_Labels extends BioController
{
  function Delete_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  public function delete_dialog($label_id)
  {
    if(!$this->logged_in)
    {
      return $this->invalid_permission_nothing();
    }
    
    $this->smarty->assign('label', $this->label_model->get($label_id));
    $this->smarty->view_s('delete_multiple_label/dialog');
  }
  
  public function delete()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }
    
    $this->more_time_limit();
    
    $label_id = $this->get_post('label_id');
    $search = $this->get_post('search');
    
    $search_tree = json_decode($search, true);
    $transform = $this->__get_transform_label('transform', 'post');
    
    $this->load->model('search_model');
    
    $seqs = $this->search_model->get_search($search_tree, array('transform' => $transform));
    
    $deleted_labels = 0;
    $deleted_from_seqs = 0;
    
    foreach($seqs as &$seq) {
      $id = $seq['id'];
      
      $total = $this->label_sequence_model->num_label($id, $label_id);
      
      if($total > 0) {
        $deleted_labels = $deleted_labels + $total;
        ++$deleted_from_seqs;
        
        $this->label_sequence_model->delete_label_seq($id, $label_id);
      }
    }
    
    $this->smarty->assign('deleted_labels', $deleted_labels);
    $this->smarty->assign('deleted_from_seqs', $deleted_from_seqs);
    
    $this->smarty->view_js('delete_multiple_label/stats');
  }
}