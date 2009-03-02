<?php

class Tree extends BioController {
  
  function Tree() {
    parent::BioController();
    $this->load->model('taxonomy_tree_model');
  }

  function index() {
    if(!$this->logged_in) {
      return;
    }

    $this->smarty->assign('title', 'Edit trees');
    $this->use_mygrid();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->view('taxonomy/trees');
  }

  function get_all() {
    if(!$this->logged_in) {
      return;
    }

    $trees = $this->taxonomy_tree_model->get_trees();

    echo json_encode($trees);
  }

  function add($name) {
    if(!$this->logged_in) {
      return;
    }

    if($this->taxonomy_tree_model->has_name($name)) {
      echo json_encode(null);
    } else {
      $id = $this->taxonomy_tree_model->add($name);
      $data = $this->taxonomy_tree_model->get($id);

      echo json_encode($data);
    }
  }

  function total_taxonomies($tree)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('taxonomy_model');
    $total = $this->taxonomy_model->count_tree($tree);

    echo $total;
  }

  function delete($id) {
    if(!$this->logged_in) {
      return;
    }

    $this->taxonomy_tree_model->delete_id($id);

    echo json_encode(true);
  }

  function edit_name() {
    if(!$this->logged_in) {
      return;
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

