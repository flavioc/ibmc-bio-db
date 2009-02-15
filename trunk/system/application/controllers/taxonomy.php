<?php

class Taxonomy extends BioController {
  function Taxonomy()
  {
    parent::BioController();
  }

  function add()
  {
    $this->smarty->assign('title', 'Add taxonomy');

    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->load->model('taxonomy_rank_model');

    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->smarty->assign('ranks', $ranks);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('rank');

    $this->smarty->view('taxonomy/add');
  }

  function do_add()
  {
    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[512]');
    $this->form_validation->set_rules('rank', 'Rank', 'trim|numeric');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    $this->load->model('taxonomy_rank_model');

    $rank = $this->get_post('rank');

    if(!$this->taxonomy_rank_model->has_id($rank)) {
      $this->set_form_error('rank', "Rank with id $rank doesn't exist.");
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('rank');

      redirect('taxonomy/add');
    } else {
      $name = $this->get_post('name');

      $this->load->model('taxonomy_model');

      $id = $this->taxonomy_model->add($name, $rank);

      redirect("taxonomy/view/$id");
    }
  }

  function view($id)
  {
    $this->smarty->assign('title', 'View taxonomy');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, JEDITABLE_SCRIPT, CONFIRM_SCRIPT, APPENDDOM_SCRIPT);
    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_name_model');
    $this->load->model('taxonomy_name_type_model');
    $this->load->model('taxonomy_rank_model');

    $types = $this->taxonomy_name_type_model->get_all();
    $taxonomy = $this->taxonomy_model->get($id);
    $names = $this->taxonomy_name_model->get_tax($id);
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $parent_id = $taxonomy['parent_id'];
    if($parent_id) {
      $parent = $this->taxonomy_model->get($parent_id);
      $this->smarty->assign('parent', $parent);
    }

    $this->smarty->assign('types', $types);
    $this->smarty->assign('taxonomy', $taxonomy);
    $this->smarty->assign('names', $names);
    $this->smarty->assign('ranks', $ranks);

    $this->smarty->view('taxonomy/view');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('tax');
    $value = $this->input->post('value');

    $this->load->model('taxonomy_model');

    $size = strlen($value);
    if($size < 3 || $size > 512) {
      $name = $this->taxonomy_model->get_name($id);
      echo $name;
      return;
    }

    $this->taxonomy_model->edit_name($id, $value);

    echo $value;
  }

  function edit_rank()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('tax');
    $value = $this->input->post('value');

    $this->load->model('taxonomy_model');

    $this->taxonomy_model->edit_rank($id, $value);

    $this->load->model('taxonomy_rank_model');

    echo $this->taxonomy_rank_model->get_name($value);
  }

  function _browse($title)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->use_paging_size();
    $this->use_autocomplete();

    $this->smarty->assign('title', 'Browse taxonomies');
    $this->smarty->assign('subtitle', $title);
    $this->smarty->load_scripts(VALIDATE_SCRIPT, APPENDDOM_SCRIPT, CONFIRM_SCRIPT, MYGRID_SCRIPT);

    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->smarty->assign('ranks', $ranks);

    $this->smarty->view('taxonomy/browse');
  }

  function browse()
  {
    $this->_browse('Search taxonomies');
  }

  function browse_parent($id)
  {
    $this->smarty->assign('child_id', $id);

    $this->load->model('taxonomy_model');

    $child_name = $this->taxonomy_model->get_name($id);

    $this->_browse('Search parent taxonomy for ' . $child_name);
  }

  function search()
  {
    if(!$this->logged_in) {
      return;
    }

    $name = $this->get_parameter('name');
    $rank = $this->get_parameter('rank');
    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    if($rank == '0') {
      $rank = null;
    }

    $this->load->model('taxonomy_model');
    $result = $this->taxonomy_model->search($name, $rank, $start, $size);

    echo json_encode($result);
  }

  function search_total()
  {
    if(!$this->logged_in) {
      return;
    }

    $name = $this->get_parameter('name');
    $rank = $this->get_parameter('rank');

    if($rank == '0') {
      $rank = null;
    }

    $this->load->model('taxonomy_model');

    echo $this->taxonomy_model->search_total($name, $rank);
  }

  function search_autocomplete()
  {
    if(!$this->logged_in) {
      return;
    }

    $what = $this->get_parameter('q');
    $limit = $this->get_parameter('limit');
    $timestamp = $this->get_parameter('timestamp');
    $rank = $this->get_parameter('rank');

    $this->load->model('taxonomy_model');

    $result = $this->taxonomy_model->search_field('name', $what, intval($rank), 0, intval($limit));

    foreach($result as $item) {
      echo $item['name'] . "\n";
    }
  }

  function _delete($id)
  {
    $this->load->model('taxonomy_model');

    $this->taxonomy_model->delete($id);
  }

  function delete($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->_delete($id);

    echo build_ok();
  }

  function delete_redirect($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->_delete($id);

    redirect('taxonomy/browse');
  }

  function set_parent($tax_id, $parent_id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('taxonomy_model');
    $this->taxonomy_model->edit_parent($tax_id, $parent_id);

    redirect('taxonomy/view/' . $tax_id);
  }
}

