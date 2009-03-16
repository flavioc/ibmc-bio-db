<?php

class Rank extends BioController {
  
  function Rank() {
    parent::BioController();
    $this->load->model('taxonomy_rank_model');
  }

  function index() {
  }

  function list_all()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Rank list');
    $this->use_mygrid();

    $this->smarty->view('rank/list');
  }

  function view($id)
  {
    $this->smarty->assign('title', 'View rank');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);

    $this->load->model('taxonomy_rank_model');
    $rank = $this->taxonomy_rank_model->get($id);
    $this->smarty->assign('rank', $rank);

    $ranks = $this->taxonomy_rank_model->get_simple_all($id);
    $this->smarty->assign('ranks', $ranks);

    $this->smarty->view('rank/view');
  }

  function add()
  {
    $this->smarty->assign('title', 'Add rank');

    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('parent_id');

    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();
    $this->smarty->assign('ranks', $ranks);

    $this->smarty->view('rank/add');
  }

  function do_add()
  {
    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[128]');
    $this->form_validation->set_rules('parent_id', 'Parent', 'trim|numeric');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    $this->load->model('taxonomy_rank_model');

    $parent = intval($this->get_post('parent_id'));
    if($parent == 0) {
      $parent = null;
    }

    if($parent && !$this->taxonomy_rank_model->has_id($parent)) {
      $this->set_form_error('parent_id',
        "Rank with id $parent doesn't exist.");
      $errors = true;
    }

    if($parent && $this->taxonomy_rank_model->parent_used($parent)) {
      $this->set_form_error('parent_id', "Rank with id $parent already being used as parent.");
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('parent_id');

      redirect('rank/add');
    } else {
      $name = $this->get_post('name');

      $id = $this->taxonomy_rank_model->add($name, $parent);

      redirect("rank/view/$id");
    }
  }

  function get_all() {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');
    $ranks = $this->taxonomy_rank_model->get_ranks($size, $start);

    $this->json_return($ranks);
  }

  function get_total() {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $total = $this->taxonomy_rank_model->get_total();

    $this->json_return($total);
  }

  function edit_name() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('rank');
    $value = $this->input->post('value');

    $result = $this->taxonomy_rank_model->edit_name($id, $value);

    if($result) {
      // Update OK
      echo $value;
    } else {
      // Name already used.
      echo $this->taxonomy_rank_model->get_name($id);
    }
  }

  function edit_parent() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('rank');
    $value = intval($this->input->post('value'));

    if($value == 0) {
      $value = null;
    }

    if($this->taxonomy_rank_model->edit_parent($id, $value)) {
      if($value) {
        echo $this->taxonomy_rank_model->get_name($value);
      } else {
        $this->return_empty();
      }
    } else {
      // parent is already being used somewhere else
      // try to get current parent name
      $name = $this->taxonomy_rank_model->get_parent_name($id);
      if($name) {
        echo $name;
      } else {
        $this->return_empty();
      }
    }
  }

  function total_taxonomies($rank)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $this->load->model('taxonomy_model');
    $total = $this->taxonomy_model->count_rank($rank);

    $this->json_return($total);
  }

  function delete($id) {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->taxonomy_rank_model->delete_id($id);

    $this->json_return(true);
  }

  function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');
    $this->taxonomy_rank_model->delete_id($id);

    redirect('rank/list_all');
  }

  function add_json($name) {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    if($this->taxonomy_rank_model->has_name($name)) {
      $this->json_return(false);
    } else {
      $id = $this->taxonomy_rank_model->add($name);
      $data = $this->taxonomy_rank_model->get_id($id);
      $this->json_return($data);
    }
  }
}

