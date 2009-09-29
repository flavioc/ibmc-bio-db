<?php

class Search extends BioController
{
  function Search()
  {
    parent::BioController();
    $this->load->model('search_model');
  }
  
  public function index()
  {
    $this->smarty->assign('title', 'Search sequences');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'label_helpers.js',
      'sequence_search.js', SELECTBOXES_SCRIPT, 'taxonomy_functions.js',
      'common_sequence.js', GETPARAMS_SCRIPT);
    $this->smarty->load_stylesheets('search.css');
    $this->use_mygrid();
    $this->use_datepicker();
    $this->use_plusminus();
    
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    
    $this->load->model('label_model');
    $this->smarty->assign('refs', $this->label_model->get_refs());
    
    $this->assign_label_types(true);
    $this->use_autocomplete();
    $this->use_thickbox();

    $type = $this->get_parameter('type');
    switch($type) {
      case 'label':
      case 'notlabel':
        $id = $this->get_parameter('id');
        if($id) {
          $this->load->model('label_model');
          $this->smarty->assign('label', $this->label_model->get($id));
        } else {
          $this->smarty->assign('label', null);
        }
        break;
    }
    
    $this->smarty->assign('type', $type);
    $this->smarty->view('sequence/search');
  }
  
  public function get_search()
  {
    $start = $this->get_post('start');
    $size = $this->get_post('size');
    $search = $this->__get_search_term('post');
    $transform = $this->__get_transform_label('transform', 'post');

    $ordering_name = $this->get_order('name', 'post');
    $ordering_update = $this->get_order('update', 'post');
    $ordering_user = $this->get_order('user_name', 'post');

    $this->json_return($this->search_model->get_search($search,
      array('start' => $start,
            'size' => $size,
            'ordering' => 
              array('name' => $ordering_name,
                    'update' => $ordering_update,
                    'user_name' => $ordering_user),
            'transform' => $transform,
            'only_public' => !$this->logged_in)));
  }

  public function get_search_total()
  {
    $search = $this->__get_search_term('post');
    $transform = $this->__get_transform_label('transform', 'post');

    $this->json_return(
      $this->search_model->get_search_total($search, $transform, !$this->logged_in));
  }
  
  public function humanize()
  {
    $tree = $this->__get_search_term('post');
    $tree_str = search_tree_to_string($tree, '<span class="compound-operator">', '</span>');

    echo $tree_str;
  }
  
  public function delete_results()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $encoded = $this->get_post('encoded_tree');
    $json = stripslashes($encoded);
    $tree = json_decode($json, true);
    
    $this->smarty->assign('encoded', $json);
    
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);

    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $this->smarty->assign('transform', $transform);
   
    $this->smarty->load_stylesheets('operations.css');
    $this->smarty->assign('title', 'Delete results');
    $this->use_mygrid();
    $this->smarty->view('sequence/delete_results');
  }
  
  public function do_delete_results()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $encoded = $this->get_post('encoded_tree');

    $json = stripslashes($encoded);
    $tree = json_decode($json, true);
    
    $transform = $this->__get_transform_label('transform', 'post');
    
    $this->load->model('search_model');
    $results = $this->search_model->get_search($tree,
        array('transform' => $transform, 'select' => 'id'));
        
    $total = 0;

    foreach($results as &$result) {
      $id = $result['id'];
      
      $this->sequence_model->delete($id);
      
      ++$total;
    }
    
    $this->smarty->assign('title', 'Delete results report');
    
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);
    
    $this->smarty->load_stylesheets('operations.css');
    $this->smarty->assign('total', $total);
    
    $this->smarty->view('sequence/delete_results_report'); 
  }
  
  public function delete_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css');
    $this->use_autocomplete();
    $this->use_impromptu();
    $this->use_blockui();

    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      FORM_SCRIPT,
      CONFIRM_SCRIPT,
      'label_helpers.js',
      'select_label.js',
      'common_sequence.js',
      'delete_multiple.js');

    $encoded = $this->get_post('encoded_tree');
    $this->smarty->assign('encoded', $encoded);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $this->smarty->assign('transform', $transform);

    $this->smarty->assign('title', 'Multiple delete label');
    $this->smarty->view('sequence/multiple_delete_label');
  }
  
  public function add_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css');
    $this->use_autocomplete();
    $this->use_thickbox();
    $this->use_datepicker();
    $this->use_blockui();

    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      FORM_SCRIPT,
      GETPARAMS_SCRIPT,
      'label_functions.js',
      'select_label.js',
      'add_multiple.js',
      'label_helpers.js',
      'taxonomy_functions.js',
      'common_sequence.js');

    $encoded = $this->get_post('encoded_tree');
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $mode = $this->get_parameter('mode');
    
    if($mode == 'add') {
      $this->smarty->assign('title', 'Multiple add label');
    } else if($mode == 'edit') {
      $this->smarty->assign('title', 'Multiple edit label');
    }
    
    $this->smarty->assign('transform', $transform);
    $this->smarty->assign('mode', $mode);
    $this->smarty->assign('encoded', $encoded);

    $this->smarty->view('sequence/multiple_add_label');
  }
  
  public function ref()
  {
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    $this->smarty->view_s('sequence/search_ref');
  }

  public function tax()
  {
    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view_s('sequence/search_tax');
  }
  
  private function __get_search_term($method = 'get')
  {
    if($method == 'get') {
      $search = $this->get_parameter('search');
    } else {
      $search = $this->get_post('search');
    }
    $search = stripslashes($search);
    $search_term = null;
    
    if($search) {
      $search_term = json_decode($search, true);
    }

    return $search_term;
  }
}