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
    $auto = $label['auto_on_creation'];

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
      echo "NOT HANDLED, PLEASE REPORT";
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
  
  function __add($seq_id, $label_id, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return "Label is already being used";
    }
    
    $label = $this->label_model->get($label_id);
    if(!$label) {
      return "Could not found label";
    }

    if($generate) {
      return $this->label_sequence_model->add_generated_label($seq_id, $label_id);
    }

    switch($label['type']) {
      case 'url':
        return $this->label_sequence_model->add_url_label($seq_id, $label_id, $this->get_post('url'));
      case 'text':
        return $this->label_sequence_model->add_text_label($seq_id, $label_id, $this->get_post('text'));
      case 'tax':
        $tax = $this->get_post('hidden_tax');

        $this->load->model('taxonomy_model');
        if(!$this->taxonomy_model->has_taxonomy($tax)) {
          $tax_name = $this->get_post('tax');
          return "Taxonomy with id $tax [$tax_name] doesn't exist";
        }

        return $this->label_sequence_model->add_tax_label($seq_id, $label_id, $tax);
      case 'ref':
        $ref = $this->get_post('hidden_ref');

        if(!$this->sequence_model->has_sequence($ref)) {
          $ref_name = $this->get_post('ref');
          return "Sequence with id $ref [$ref_name] doesn't exist";
        }

        return $this->label_sequence_model->add_ref_label($seq_id, $label_id, $ref);
      case 'position':
        $start = $this->get_post('start');
        $length = $this->get_post('length');
        
        return $this->label_sequence_model->add_position_label($seq_id, $label_id, $start, $length);
      case 'obj':
        try {
          $data = $this->__read_uploaded_file('file', $this->__get_obj_label_config());
          return $this->label_sequence_model->add_obj_label($seq_id, $label_id,
                $data['filename'], $data['bytes']);
        } catch(Exception $e) {
          return $e->getMessage();
        }
      case 'integer':
        return $this->label_sequence_model->add_integer_label($seq_id, $label_id, $this->get_post('integer'));
      case 'bool':
        return $this->label_sequence_model->add_bool_label($seq_id, $label_id,
            $this->get_post('boolean') ? TRUE : FALSE);
      default:
        return "Label type is invalid";
    }
  }
  
  function __get_generate()
  {
    $ret = $this->get_post('generate_check');

    return $ret ? TRUE : FALSE;
  }

  function add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $generate = $this->__get_generate();
    
    $this->json_return($this->__add($seq_id, $label_id, $generate));
  }
}