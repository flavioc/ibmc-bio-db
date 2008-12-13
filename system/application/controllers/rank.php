<?php

class Rank extends BioController {
  
  function Rank() {
    parent::BioController();
    $this->load->model('taxonomy_rank_model');
  }

  function index() {
    if(!$this->logged_in) {
      return;
    }

    $this->smarty->assign('title', 'Edit ranks');
    $this->smarty->load_scripts(CONFIRM_SCRIPT, JEDITABLE_SCRIPT, VALIDATE_SCRIPT, APPENDDOM_SCRIPT);

    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->view('ranks');
  }

  function edit() {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = $this->input->post('value');

    $result = $this->taxonomy_rank_model->edit($id, $value);

    if($result) {
      // Update OK
      echo $value;
    } else {
      // Name already used.
      echo $this->taxonomy_rank_model->get_name($id);
    }
  }

  function delete($id) {
    if(!$this->logged_in) {
      return;
    }

    $this->taxonomy_rank_model->delete_id($id);

    echo build_ok();
  }

  function add($name) {
    if(!$this->logged_in) {
      return;
    }

    if($this->taxonomy_rank_model->has_name($name)) {
      echo "$name is already on the database";
    } else {
      $id = $this->taxonomy_rank_model->add($name);
      echo build_ok_id($id);
    }
  }
}

