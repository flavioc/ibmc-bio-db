<?php

class Add_Labels extends BioController {
  function Add_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  function add_dialog($seq_id, $label_id)
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
          $this->smarty->view_s('new_label/text');
          break;
        case 'integer':
          $this->smarty->view_s('new_label/integer');
          break;
        case 'url':
          $this->smarty->view_s('new_label/url');
          break;
        case 'obj':
          $this->smarty->view_s('new_label/obj');
          break;
        case 'bool':
          $this->smarty->view_s('new_label/bool');
          break;
        case 'position':
          $this->smarty->view_s('new_label/position');
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
  
  function add_auto()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }
    
    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return $this->json_return("Label with id $label_id is already being used");
    } else {
      $this->label_sequence_model->add_auto_label_id($seq_id, $label_id);
      return $this->json_return(true);
    }
  }
  
  function add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $generate = $this->get_post('generate_check') ? TRUE : FALSE;
    
    $this->json_return($this->__add_label($seq_id, $label_id, $generate));
  }
}