<?php

class Tree extends BioController {
  
  function Tree() {
    parent::BioController();
    $this->load->model('taxonomy_tree_model');
  }

  function index() {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Edit trees');
    $this->use_mygrid();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->view('taxonomy/trees');
  }

  function get_all() {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $trees = $this->taxonomy_tree_model->get_trees();

    $this->json_return($trees);
  }

  function add($name) {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    if($this->taxonomy_tree_model->has_name($name)) {
      $this->json_return(false);
    } else {
      $id = $this->taxonomy_tree_model->add($name);
      $data = $this->taxonomy_tree_model->get($id);

      $this->json_return($data);
    }
  }

  function total_taxonomies($tree)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $this->load->model('taxonomy_model');
    $total = $this->taxonomy_model->count_tree($tree);

    $this->json_return($total);
  }

  function delete($id) {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->taxonomy_tree_model->delete_id($id);

    $this->json_return(true);
  }

  function edit_name() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = $this->input->post('value');

    $result = $this->taxonomy_tree_model->edit($id, $value);

    if($result) {
      // Update OK
      echo $value;
    } else {
      // Name already used.
      echo $this->taxonomy_tree_model->get_name($id);
    }
  }
}

