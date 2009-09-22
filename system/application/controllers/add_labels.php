<?php

class Add_Labels extends BioController
{
  public function Add_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  public function add_dialog($seq_id, $label_id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_model->get($label_id);

    $this->smarty->assign('sequence', $this->sequence_model->get($seq_id));
    $this->smarty->assign('label', $label);

    $editable = $label['editable'];
    $auto = $label['code'];

    if(!$editable && $auto) {
      $this->smarty->view_s('new_label/auto');
    } else if($editable) {
      $type = $label['type'];

      switch($type) {
        case 'text':
        case 'integer':
        case 'float':
        case 'url':
        case 'obj':
        case 'bool':
        case 'position':
        case 'date':
          $this->smarty->view_s("new_label/$type");
          break;
        case 'ref':
          $this->load->model('user_model');
          $this->smarty->assign('users', $this->user_model->get_users_all());
          $this->smarty->view_s('new_label/ref');
          break;
        case 'tax':
          $this->load->model('taxonomy_rank_model');
          $this->load->model('taxonomy_tree_model');
          
          $this->smarty->assign('ranks', $this->taxonomy_rank_model->get_ranks());
          $this->smarty->assign('trees', $this->taxonomy_tree_model->get_trees());

          $this->smarty->view_s('new_label/tax');
          break;
      }
    } else {
      $this->smarty->view_s('common_label/malformed.tpl');
    }
  }
  
  public function add_auto()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }
    
    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return $this->json_return("Label with id $label_id is already being used");
    } else {
      return $this->json_return($this->label_sequence_model->add_auto_label_id($seq_id, $label_id) ? TRUE : FALSE);
    }
  }
  
  public function add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $generate = $this->get_post('generate_check') ? TRUE : FALSE;
    $param = $this->get_post('param');
    
    $this->json_return($this->__add_label($seq_id, $label_id, $generate, $param));
  }
}