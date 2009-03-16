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

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();
    $this->smarty->assign('trees', $trees);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('rank');
    $this->smarty->fetch_form_row('tree');
    $this->smarty->fetch_form_row('parent_id');

    $parent_id = $this->smarty->get_initial_var('parent_id');
    if($parent_id) {
      if($parent_id == '0') {
        $parent_id = null;
      } else {
        $this->load->model('taxonomy_model');
        $parent_name = $this->taxonomy_model->get_name($parent_id);

        $this->smarty->assign('parent_name', $parent_name);
        $this->smarty->assign('parent_id', $parent_id);
      }
    }

    $rank_id = $this->smarty->get_initial_var('rank');
    if(!$rank_id && $parent_id) {
      $this->load->model('taxonomy_model');
      $parent_rank = $this->taxonomy_model->get_rank($parent_id);
      $child_rank = $this->taxonomy_rank_model->get_child($parent_rank);

      $this->smarty->set_initial_var('rank', $child_rank);
    }

    $tree_id = $this->smarty->get_initial_var('tree');
    if(!$tree_id && $parent_id) {
      $this->load->model('taxonomy_model');
      $tree_id = $this->taxonomy_model->get_tree($parent_id);

      $this->smarty->set_initial_var('tree', $tree_id);
    }

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

    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_rank_model');

    $rank = intval($this->get_post('rank'));
    if($rank == 0) {
      $rank = null;
    }

    if($rank && !$this->taxonomy_rank_model->has_id($rank)) {
      $this->set_form_error('rank', "Rank with id $rank doesn't exist.");
      $errors = true;
    }

    $this->load->model('taxonomy_tree_model');

    $tree = intval($this->get_post('tree'));
    if($tree == 0) {
      $tree = null;
    }

    if($tree && !$this->taxonomy_tree_model->has_id($tree)) {
      $this->set_form_error('tree', "Tree with id $tree doesn't exist.");
      $errors = true;
    }

    $parent_id = $this->get_post('parent_id');
    if($parent_id) {
      $parent_id = intval($parent_id);
      if(!$this->taxonomy_model->has_id($parent_id)) {
        $this->set_form_error('parent_id', "Parent with id $parent_id doesn't exist.");
        $errors = true;
      }

      if($parent_id == 0) {
        $parent_id = null;
      }
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('rank');
      $this->assign_row_data('tree');
      $this->assign_row_data('parent_id');

      redirect('taxonomy/add');
    } else {
      $name = $this->get_post('name');

      $this->load->model('taxonomy_model');

      $id = $this->taxonomy_model->add($name, $rank, $tree, $parent_id);

      redirect("taxonomy/view/$id");
    }
  }

  function select_parent($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $this->__assign_search_components();

    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_rank_model');

    $taxonomy = $this->taxonomy_model->get($id);
    $tree = $taxonomy['tree_id'];
    $rank = intval($taxonomy['rank_id']);

    $this->smarty->set_initial_var('tree', $tree);
    $this->smarty->set_initial_var('rank', $this->taxonomy_rank_model->get_parent($rank));

    $this->smarty->assign('taxonomy', $id);
    $this->smarty->view_s('taxonomy/select_parent');
  }

  function view($id)
  {
    $this->smarty->assign('title', 'View taxonomy');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'taxonomy_functions.js', AUTOCOMPLETE_SCRIPT);
    $this->use_mygrid();
    $this->use_thickbox();

    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_name_model');
    $this->load->model('taxonomy_name_type_model');
    $this->load->model('taxonomy_rank_model');
    $this->load->model('taxonomy_tree_model');

    $types = $this->taxonomy_name_type_model->get_all();
    $taxonomy = $this->taxonomy_model->get($id);
    $ranks = $this->taxonomy_rank_model->get_ranks();
    $trees = $this->taxonomy_tree_model->get_trees();

    $parent_id = $taxonomy['parent_id'];
    if($parent_id) {
      $parent = $this->taxonomy_model->get($parent_id);
      $this->smarty->assign('parent', $parent);
    }

    $this->smarty->assign('types', $types);
    $this->smarty->assign('taxonomy', $taxonomy);
    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view('taxonomy/view');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
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
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('tax');
    $value = intval($this->input->post('value'));
    if($value == 0) {
      $value = null;
    }

    $this->load->model('taxonomy_model');

    $this->taxonomy_model->edit_rank($id, $value);

    $this->load->model('taxonomy_rank_model');

    if($value == null) {
      $this->return_empty();
    } else {
      echo $this->taxonomy_rank_model->get_name($value);
    }
  }

  function edit_tree()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('tax');
    $value = intval($this->input->post('value'));
    if($value == 0) {
      $value = null;
    }

    $this->load->model('taxonomy_model');

    $this->taxonomy_model->edit_tree($id, $value);

    $this->load->model('taxonomy_tree_model');


    if($value == null) {
      $this->return_empty();
    } else {
      echo $this->taxonomy_tree_model->get_name($value);
    }
  }

  function get_taxonomy($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->load->model('taxonomy_model');

    $this->json_return($this->taxonomy_model->get($id));
  }

  function taxonomy_childs($tax, $tree)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $this->load->model('taxonomy_model');

    if($tree == '0') {
      $tree = NULL;
    }

    if($tax == '0') {
      $tax = NULL;
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    $this->json_return($this->taxonomy_model->get_taxonomy_childs($tax, $tree, $start, $size));
  }

  function total_taxonomy_childs($tax, $tree)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $this->load->model('taxonomy_model');

    if($tree == '0') {
      $tree = NULL;
    }

    if($tax == '0') {
      $tax = NULL;
    }

    $this->json_return($this->taxonomy_model->count_taxonomy_childs($tax, $tree));
  }

  function tree_browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Browse taxonomies');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    $this->use_mygrid();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();
    $this->smarty->assign('trees', $trees);

    $this->smarty->view('taxonomy/tree_browse');
  }

  function browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_autocomplete();

    $this->smarty->assign('title', 'Browse taxonomies');
    $this->smarty->assign('subtitle', 'Search taxonomies');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'taxonomy_functions.js');
    $this->use_mygrid();

    $this->__assign_search_components();

    $this->smarty->view('taxonomy/browse');
  }

  function __assign_search_components()
  {
    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);
  }

  function search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $name = $this->get_parameter('name');
    $rank = intval($this->get_parameter('rank'));
    $tree = intval($this->get_parameter('tree'));
    $start = intval($this->get_parameter('start'));
    $size = intval($this->get_parameter('size'));

    if($rank == 0) {
      $rank = null;
    }

    if($tree == 0) {
      $tree = null;
    }

    $this->load->model('taxonomy_model');

    $result = $this->taxonomy_model->search($name, $rank, $tree, $start, $size);

    $this->json_return($result);
  }

  function search_total()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $name = $this->get_parameter('name');
    $rank = intval($this->get_parameter('rank'));
    $tree = intval($this->get_parameter('tree'));

    if($rank == 0) {
      $rank = null;
    }

    if($tree == 0) {
      $tree = null;
    }

    $this->load->model('taxonomy_model');

    echo $this->taxonomy_model->search_total($name, $rank, $tree);
  }

  function search_autocomplete()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    $what = $this->get_parameter('q');
    $limit = $this->get_parameter('limit');
    $timestamp = $this->get_parameter('timestamp');
    $rank = $this->get_parameter('rank');
    $tree = $this->get_parameter('tree');

    $this->load->model('taxonomy_model');

    if($tree == '0') {
      $tree = null;
    } else {
      $tree = intval($tree);
    }

    $result = $this->taxonomy_model->search_field('name', $what, intval($rank),
      $tree, 0, intval($limit));

    foreach($result as $item) {
      echo $item['name'] . "\n";
    }
  }

  function _delete($id)
  {
    $this->load->model('taxonomy_model');

    $this->taxonomy_model->delete($id);
  }

  function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');

    $this->_delete($id);

    redirect('taxonomy/browse');
  }

  function set_parent()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $tax_id = $this->get_post('tax_id');
    $parent_id = $this->get_post('hidden_tax');

    $this->load->model('taxonomy_model');
    $this->taxonomy_model->edit_parent($tax_id, $parent_id);

    redirect('taxonomy/view/' . $tax_id);
  }
}

