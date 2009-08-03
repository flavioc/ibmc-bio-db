<?php

class Multiple_Labels extends BioController {
  private $search_tree = null;
  private $multiple = FALSE;
  private $update = FALSE;
  private $label = null;
  private $label_type = null;
  private $label_id = null;
  private $seqs = null;
  private $generate = FALSE;
  private $addnew = FALSE;
  private $mode = null;
  private $edit_mode = false;
  private $add_mode = false;
  
  // data
  private $text = null;
  private $integer = null;
  private $url = null;
  private $boolean = null;
  private $start = null;
  private $length = null;
  private $tax = null;
  private $ref = null;
  private $upload_error = false;
  private $filename = null;
  private $bytes = null;
  private $date = null;
  
  // stats
  private $count_new_multiple = 0;
  private $count_regenerate = 0;
  private $count_new = 0;
  private $count_new_generated = 0;
  private $count_updated = 0;
  private $count_new_multiple_generated = 0;
  private $count_invalid = 0;

  function Multiple_Labels()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }

  function add_dialog($label_id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $mode = $this->get_parameter('mode');
    $this->smarty->assign('mode', $mode);
    
    $label = $this->label_model->get($label_id);
    $this->smarty->assign('label', $label);

    $editable = $label['editable'];
    $auto = $label['code'];

    if(!$editable && $auto) {
      $this->smarty->view_s('add_multiple_label/auto');
    } else if($editable) {
      $type = $label['type'];

      switch($type) {
        case 'text':
        case 'integer':
        case 'url':
        case 'bool':
        case 'position':
        case 'obj':
        case 'date':
          $this->smarty->view_s("add_multiple_label/$type");
          break;
        
        case 'tax':
          $this->load->model('taxonomy_rank_model');
          $this->load->model('taxonomy_tree_model');
          $this->smarty->assign('ranks', $this->taxonomy_rank_model->get_ranks());
          $this->smarty->assign('trees', $this->taxonomy_tree_model->get_trees());
          
          $this->smarty->view_s('add_multiple_label/tax');
          break;
          
        case 'ref':
          $this->load->model('user_model');
          $this->smarty->assign('users', $this->user_model->get_users_all());
          
          $this->smarty->view_s('add_multiple_label/ref');
          break;
      }
    } else {
      $this->smarty->view_s('common_label/malformed.tpl');
    }
  }

  // fetch search tree, sequence list and flags
  function __get_info()
  {
    $this->label_id = $this->get_post('label_id');
    $this->label = $this->label_model->get($this->label_id);
    $this->label_type = $this->label['type'];

    $search = stripslashes($this->get_post('search'));
    $this->search_tree = json_decode($search, true);
    
    $this->seqs = $this->label_sequence_model->get_search($this->search_tree);
    $this->multiple = json_decode($this->get_post('multiple'));
    
    $update = $this->get_post('update');
    if($update) {
      $this->update = json_decode($update);
    }
    
    $this->generate = json_decode($this->get_post('generate_check'));
    
    $addnew = $this->get_post('addnew');
    if($addnew) {
      $this->addnew = json_decode($addnew);
    }
    
    $this->mode = $this->get_post('mode');
    if($this->mode == 'add') {
      $this->add_mode = true;
      $this->edit_mode = false;
    } else if($this->mode == 'edit') {
      $this->add_mode = false;
      $this->edit_mode = true;
    }
  }

  function __can_do_multiple()
  {
    return $this->multiple && $this->label['multiple'];
  }

  function add_auto_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $this->__get_info();

    foreach($this->seqs as &$seq) {
      $seq_id = $seq['id'];

      if($this->label_sequence_model->sequence_has_label($seq_id, $this->label_id)) {
        if($this->__can_do_multiple()) {
          if($this->label_sequence_model->add_auto_label($seq_id, $this->label)) {
            ++$this->count_new_multiple;
          } else {
            ++$this->count_invalid;
          }
        } else if($this->edit_mode || $this->update) {
          $this->__regenerate_label($seq_id);
        }
      } else if($this->add_mode || ($this->edit_mode && $this->addnew)){
        if($this->label_sequence_model->add_auto_label($seq_id, $this->label)) {
          ++$this->count_new_generated;
        } else {
          ++$this->count_invalid;
        }
      }
    }

    $this->__show_stats();
  }

  function __show_stats()
  {
    $this->smarty->assign('count_new_multiple', $this->count_new_multiple);
    $this->smarty->assign('count_new', $this->count_new);
    $this->smarty->assign('count_regenerate', $this->count_regenerate);
    $this->smarty->assign('count_new_generated', $this->count_new_generated);
    $this->smarty->assign('count_updated', $this->count_updated);
    $this->smarty->assign('count_new_multiple_generated', $this->count_new_multiple_generated);
    $this->smarty->assign('count_invalid', $this->count_invalid);

    $this->smarty->view_js('add_multiple_label/auto');
  }

  function __regenerate_label($seq_id)
  {
    $id = $this->label_sequence_model->get_label_id($seq_id, $this->label_id);
    if($this->label_sequence_model->edit_auto_label($id)) {
      ++$this->count_regenerate;
    } else {
      ++$this->count_invalid;
    }
  }

  function __add_label_multiple($seq_id)
  {
    if($this->__add_label_common($seq_id)) {
      ++$this->count_new_multiple;
    } else {
      ++$this->count_invalid;
    }
  }
  
  function __get_values()
  {
    switch($this->label_type) {
      case 'text':
        $this->text = $this->get_post('text');
        break;
      case 'integer':
        $this->integer = $this->get_post('integer');
        break;
      case 'url':
        $this->url = $this->get_post('url');
        break;
      case 'bool':
        $this->boolean = $this->get_post('boolean') ? TRUE : FALSE;
        break;
      case 'date':
        $this->date = $this->get_post('date');
        break;
      case 'position':
        $this->start = $this->get_post('start');
        $this->length = $this->get_post('length');
        break;
      case 'tax':
        $this->tax = $this->get_post('hidden_tax');
        break;
      case 'ref':
        $this->ref = $this->get_post('hidden_ref');
        break;
      case 'obj':
        try {
          $data = $this->__read_uploaded_file('file', $this->__get_obj_label_config());
          $this->filename = $data['filename'];
          $this->bytes = $data['bytes'];
        } catch(Exception $e) {
          $this->upload_error = true;
        }
        break;
    }
  }

  function __add_label_common($seq_id)
  {
    switch($this->label_type) {
    case 'text':
      return $this->label_sequence_model->add_text_label($seq_id, $this->label_id, $this->text);
    case 'integer':
      return $this->label_sequence_model->add_integer_label($seq_id, $this->label_id, $this->integer);
    case 'url':
      return $this->label_sequence_model->add_url_label($seq_id, $this->label_id, $this->url);
    case 'bool':
      return $this->label_sequence_model->add_bool_label($seq_id, $this->label_id, $this->boolean);
    case 'position':
      return $this->label_sequence_model->add_position_label($seq_id, $this->label_id, $this->start, $this->length);
    case 'tax':
      return $this->label_sequence_model->add_tax_label($seq_id, $this->label_id, $this->tax);
    case 'ref':
      return $this->label_sequence_model->add_ref_label($seq_id, $this->label_id, $this->ref);
    case 'obj':
      return $this->label_sequence_model->add_obj_label($seq_id, $this->label_id, $this->filename, $this->bytes);
    case 'date':
      return $this->label_sequence_model->add_date_label($seq_id, $this->label_id, $this->date);
    }
    
    return false;
  }

  function __add_label($seq_id)
  {
    if($this->__add_label_common($seq_id)) {
      ++$this->count_new;
    } else {
      ++$this->count_invalid;
    }
  }

  function __edit_label($id)
  {
    $ret = false;
    
    switch($this->label_type) {
    case 'text':
      $ret = $this->label_sequence_model->edit_text_label($id, $this->text);
      break;
    case 'integer':
      $ret = $this->label_sequence_model->edit_integer_label($id, $this->integer);
      break;
    case 'url':
      $ret = $this->label_sequence_model->edit_url_label($id, $this->url);
      break;
    case 'bool':
      $ret = $this->label_sequence_model->edit_bool_label($id, $this->boolean);
      break;
    case 'position':
      $ret = $this->label_sequence_model->edit_position_label($id, $this->start, $this->length);
      break;
    case 'tax':
      $ret = $this->label_sequence_model->edit_tax_label($id, $this->tax);
      break;
    case 'ref':
      $ret = $this->label_sequence_model->edit_ref_label($id, $this->ref);
      break;
    case 'obj':
      $ret = $this->label_sequence_model->edit_obj_label($id, $this->filename, $this->bytes);
      break;
    case 'date':
      $ret = $this->label_sequence_model->edit_date_label($id, $this->date);
      break;
    }

    if($ret) {
      ++$this->count_updated;
    } else {
      ++$this->count_invalid;
    }
  }
  
  function __iterate_seqs()
  {
    foreach($this->seqs as &$seq) {
      $seq_id = $seq['id'];

      if($this->label_sequence_model->sequence_has_label($seq_id, $this->label_id)) {
        if($this->__can_do_multiple()) {
          if($this->generate) {
            $this->label_sequence_model->add_generated_label($seq_id, $this->label_id);
            ++$this->count_new_multiple_generated;
          } else {
            $this->__add_label_multiple($seq_id);
          }
        } else {
          if(($this->add_mode && $this->update) || $this->edit_mode) {
            $id = $this->label_sequence_model->get_label_id($seq_id, $this->label_id);
            if($this->generate) {
              $this->label_sequence_model->edit_auto_label($id);
              ++$this->count_regenerate;
            } else {
              $this->__edit_label($id);
            }
          }
        }
      } else if($this->add_mode || ($this->edit_mode && $this->addnew)){
        if($this->generate) {
          $this->label_sequence_model->add_generated_label($seq_id, $this->label_id);
          ++$this->count_new_generated;
        } else {
          $this->__add_label($seq_id);
        }
      }
    }
  }

  function add_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $this->__get_info();
    $this->__get_values();
    
    if(!$this->upload_error) {
      $this->__iterate_seqs();
    }
    
    $this->__show_stats();
  }
}
