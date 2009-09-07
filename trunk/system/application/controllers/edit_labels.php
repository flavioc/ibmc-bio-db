<?php

class Edit_Labels extends BioController
{
  function Edit_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  public function edit_dialog($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_sequence_model->get($id);
    $this->smarty->assign('label', $label);
    $this->smarty->assign('label_id', $label['label_id']);

    $seq_id = $label['seq_id'];
    $sequence = $this->sequence_model->get($seq_id);
    $this->smarty->assign('sequence', $sequence);
    $this->smarty->assign('sequence_id', $sequence['id']);

    $editable = $label['editable'];
    $auto = $label['code'];

    if(!$editable && $auto) {
      $this->smarty->view_s('edit_label/auto');
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
          $this->smarty->view_s("edit_label/$type");
          break;
        case 'ref':
          $this->load->model('user_model');
          $this->smarty->assign('users', $this->user_model->get_users_all());
          $this->smarty->view_s('edit_label/ref');
          break;
        case 'tax':
          $this->load->model('taxonomy_rank_model');
          $this->load->model('taxonomy_tree_model');
          
          $this->smarty->assign('ranks', $this->taxonomy_rank_model->get_ranks());
          $this->smarty->assign('trees', $this->taxonomy_tree_model->get_trees());

          $this->smarty->view_s('edit_label/tax');
          break;
      }
    } else {
      $this->smarty->view_s('common_label/malformed.tpl');
    }
  }
  
  public function edit_auto()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');
    
    if($this->label_sequence_model->label_exists($id)) {
      $ret = $this->label_sequence_model->edit_auto_label($id);

      if($ret) {
        return $this->json_return(true);
      } else {
        return $this->json_return("Error generating label");
      }
    } else {
      return $this->json_return("Label with id $id doesn't exist");
    }
  }
  
  public function edit()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');
    $generate = $this->get_post('generate_check') ? TRUE : FALSE;
    
    return $this->json_return($this->__edit_label($id, $generate));
  }
}