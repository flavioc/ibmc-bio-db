<?php

class Edit_Labels extends BioController {
  function Edit_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }
  
  function edit_dialog($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_sequence_model->get($id);
    $this->smarty->assign('label', $label);

    $seq_id = $label['seq_id'];
    $sequence = $this->sequence_model->get($seq_id);
    $this->smarty->assign('sequence', $sequence);

    $editable = $label['editable'];
    $auto = $label['auto_on_creation'];

    if(!$editable && $auto) {
      $this->smarty->view_s('edit_label/auto');
    } else if($editable) {
      $type = $label['type'];

      switch($type) {
        case 'text':
          $this->smarty->view_s('edit_label/text');
          break;
        case 'integer':
          $this->smarty->view_s('edit_label/integer');
          break;
        case 'url':
          $this->smarty->view_s('edit_label/url');
          break;
        case 'obj':
          $this->smarty->view_s('edit_label/obj');
          break;
        case 'bool':
          $this->smarty->view_s('edit_label/bool');
          break;
        case 'position':
          $this->smarty->view_s('edit_label/position');
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
    }
  }
  
  function edit_auto()
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
  
  function __edit($id, $generate)
  {
    if(!$this->label_sequence_model->label_exists($id)) {
      return "Label/Sequence with id $id doesn't exist";
    }
    
    if($generate) {
      if($this->label_sequence_model->edit_auto_label($id)) {
        return true;
      }

      return "Error generating label/sequence $id";
    }
    
    $label = $this->label_sequence_model->get($id);
    
    switch($label['type']) {
      case 'bool':
        return $this->label_sequence_model->edit_bool_label($id, $this->get_post('boolean') ? TRUE : FALSE);
      case 'integer':
        return $this->label_sequence_model->edit_integer_label($id, $this->get_post('integer'));
      case 'obj':
        try {
          $data = $this->__read_uploaded_file('file', $this->__get_obj_label_config());
          return $this->label_sequence_model->edit_obj_label($id, $data['filename'], $data['bytes']);
        } catch(Exception $e) {
          return $e->getMessage();
        }
      case 'position':
        return $this->label_sequence_model->edit_position_label($id,
            $this->get_post('start'), $this->get_post('length'));
      case 'ref':
        $ref = $this->get_post('hidden_ref');

        if(!$this->sequence_model->has_sequence($ref)) {
          $ref_name = $this->get_post('ref');
          return "Sequence with id $ref [$ref_name] doesn't exist";
        }

        return $this->label_sequence_model->edit_ref_label($id, $ref);      
      case 'tax':
        $tax = $this->get_post('hidden_tax');

        $this->load->model('taxonomy_model');
        if(!$this->taxonomy_model->has_taxonomy($tax)) {
          $tax_name = $this->get_post('tax');
          return "Taxonomy with id $tax [$tax_name] doesn't exist";
        }

        return $this->label_sequence_model->edit_tax_label($id, $tax);
      case 'text':
        return $this->label_sequence_model->edit_text_label($id, $this->get_post('text'));
      case 'url':
        return $this->label_sequence_model->edit_url_label($id, $this->get_post('url'));
      default:
        return "Label/Sequence id $id with invalid type";
    }
  }
  
  function edit()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');
    $generate = $this->get_post('generate_check') ? TRUE : FALSE;
    
    return $this->json_return($this->__edit($id, $generate));
  }
}