<?php

class Change_Labels extends BioController
{
  function Change_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  public function change_dialog($seq_id, $label_id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_model->get($label_id);

    $this->smarty->assign('sequence', $this->sequence_model->get($seq_id));
    $this->smarty->assign('label_info', $label);
    
    $label_seq = $this->label_sequence_model->get_label_info($seq_id, $label_id);
    $label_seq['code'] = $label['code']; // to show generate box
    $this->smarty->assign('label', $label_seq);
    
    $this->smarty->assign('toadd', $label['multiple'] || !$label_seq);

    $editable = $label['editable'];
    $auto = $label['code'];

    if(!$editable && $auto) {
      $this->smarty->view_s('change_label/auto');
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
          $this->smarty->view_s("change_label/$type");
          break;
        case 'ref':
          $this->load->model('user_model');
          $this->smarty->assign('users', $this->user_model->get_users_all());
          $this->smarty->view_s('change_label/ref');
          break;
        case 'tax':
          $this->load->model('taxonomy_rank_model');
          $this->load->model('taxonomy_tree_model');
          
          $this->smarty->assign('ranks', $this->taxonomy_rank_model->get_ranks());
          $this->smarty->assign('trees', $this->taxonomy_tree_model->get_trees());

          $this->smarty->view_s('change_label/tax');
          break;
      }
    } else {
      $this->smarty->view_s('common_label/malformed.tpl');
    }
  }
  
  public function auto_change()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }
    
    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $this->smarty->assign('seq_id', $seq_id);
    
    $label_seq = $this->label_sequence_model->get_label_info($seq_id, $label_id);
      
    if($label_seq) {
      if($label_seq['multiple']) {
        $this->label_sequence_model->add_generated_label($seq_id, $label_id);
      } else {
        $this->label_sequence_model->edit_auto_label($label_seq['id']);
      }
    } else {
      $this->label_sequence_model->add_generated_label($seq_id, $label_id);
    }
      
    $this->smarty->view_js('change_label/success');
  }
  
  public function change()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }
    
    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $generate = $this->get_post('generate_check') ? TRUE : FALSE;
    $this->smarty->assign('seq_id', $seq_id);

    $label_seq = $this->label_sequence_model->get_label_info($seq_id, $label_id);
    $ret = null;
      
    if($label_seq) {
      if($label_seq['multiple']) {
        $ret = $this->__add_label($seq_id, $label_id, $generate);
      } else {
        $ret = $this->__edit_label($label_seq['id'], $generate);
      }
    } else {
      $ret = $this->__add_label($seq_id, $label_id, $generate);
    }
      
    if(is_string($ret)) {
      $this->smarty->assign('error', $ret);
      $this->smarty->view_js('change_label/error');
    } else {
      $this->smarty->view_js('change_label/success');
    }
  }
}