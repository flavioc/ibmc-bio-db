<?php

class Multiple_Labels extends BioController {
  private $search_tree = null;
  private $multiple = FALSE;
  private $update = FALSE;
  private $label = null;
  private $label_id = null;
  private $seqs = null;

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

    $label = $this->label_model->get($label_id);

    $this->smarty->assign('label', $label);

    $editable = $label['editable'];
    $auto = $label['auto_on_creation'];

    if(!$editable && $auto) {
      $this->smarty->view_s('add_multiple_label/auto');
    } else {
    }
  }

  // fetch search tree, sequence list and flags
  function __get_info()
  {
    $this->label_id = $this->get_post('label_id');
    $this->label = $this->label_model->get($this->label_id);
    $search = $this->get_post('search');
    $search = stripslashes($this->get_post('search'));
    if($search) {
      $this->search_tree = json_decode($search, true);
    }
    $this->seqs = $this->label_sequence_model->get_search($this->search_tree);
    $this->multiple = json_decode($this->get_post('multiple'));
    $this->update = json_decode($this->get_post('update'));
  }

  function add_auto_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->__get_info();

    $count_new_multiple = 0;
    $count_regenerate = 0;
    $count_new = 0;

    foreach($this->seqs as &$seq) {
      $seq_id = $seq['id'];

      if($this->label_sequence_model->sequence_has_label($seq_id, $this->label_id)) {
        if($this->multiple && $this->label['multiple']) {
          $this->label_sequence_model->add_auto_label($seq_id, $this->label);
          ++$count_new_multiple;
        } else if($this->update) {
          $this_label = $this->label_sequence_model->get_label_info($seq_id, $this->label_id);
          $this->label_sequence_model->regenerate_label($seq_id, $this_label);
          ++$count_regenerate;
        }
      } else {
        $this->label_sequence_model->add_auto_label($seq_id, $this->label);
        ++$count_new;
      }
    }

    $this->smarty->assign('count_new_multiple', $count_new_multiple);
    $this->smarty->assign('count_new', $count_new);
    $this->smarty->assign('count_regenerate', $count_regenerate);

    $this->smarty->view_js('add_multiple_label/auto');
  }
}
