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
      'common_sequence.js', GETPARAMS_SCRIPT, FORM_SCRIPT, 'jquery.plot.js');
    $this->smarty->load_stylesheets('search.css', 'graph.css');
    $this->use_mygrid();
    $this->use_datepicker();
    $this->use_plusminus();
    $this->use_blockui();
    
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    
    $this->load->model('label_model');
    $this->smarty->assign('refs', $this->label_model->get_refs());
    
    $this->smarty->assign('positions', $this->label_model->get_positions());
    
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
    $this->smarty->view('search/index');
  }
  
  public function get_search()
  {
    $start = $this->get_post('start');
    $size = $this->get_post('size');
    $search = $this->__get_search_term('post');
    $transform = $this->__get_transform_label('transform', 'post');
    $labels = $this->get_post('labels');
    $disable_ordering = $this->get_post('disable_ordering');

    $ordering_name = $this->get_order('name', 'post');
    $ordering_update = $this->get_order('update', 'post');
    $ordering_user = $this->get_order('user_name', 'post');

    $data = $this->search_model->get_search($search,
      array('start' => $start,
            'size' => $size,
            'enable_ordering' => !$disable_ordering,
            'ordering' => 
              array('name' => $ordering_name,
                    'update' => $ordering_update,
                    'user_name' => $ordering_user),
            'transform' => $transform,
            'only_public' => !$this->logged_in));
            
    if($labels && $labels != '') {
      $names = explode('|', $labels);
      $this->load->model('label_model');
      $this->load->model('label_sequence_model');
      
      foreach($names as $name) {
        $label_id = $this->label_model->get_id_by_name($name);
        
        foreach($data as &$row) {
          $seq_id = $row['id'];
          $instance_info = $this->label_sequence_model->get_instances_info($seq_id, $label_id, !$this->logged_in);
          $row[$name] = $instance_info;
        }
      }
    }
            
    $this->json_return($data);
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
  
  public function sql()
  {
    $tree = $this->__get_search_term('post');
    
    $transform = $this->__get_transform_label('transform', 'post');
    
    $sql = $this->search_model->get_sql($tree, array('transform' => $transform, 'only_public' => !$this->logged_in, 'select' => 'id, name'));
    
    echo stripslashes($sql);
  }
  
  public function delete_results()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $tree = $this->__get_search_term('post', 'encoded_tree', $json);
    
    $this->smarty->assign('encoded', addmyslashes($json));
    $this->smarty->assign('encoded_no_slashes', $json);
    
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);

    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $this->smarty->assign('transform', $transform);
   
    $this->smarty->load_stylesheets('operations.css');
    $this->smarty->assign('title', 'Delete results');
    $this->use_mygrid();
    $this->smarty->view('search/delete_results');
  }
  
  public function do_delete_results()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->load->model('sequence_model');
    
    $tree = $this->__get_search_term('post', 'encoded_tree', $json);
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
    
    $this->smarty->view('search/delete_results_report'); 
  }
  
  public function delete_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css', 'operations.css');
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

    $tree = $this->__get_search_term('post', 'encoded_tree', $encoded, $raw);
    $this->smarty->assign('encoded', addmyslashes($encoded));
    $this->smarty->assign('encoded_no_slashes', $encoded);
    
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);
    
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $this->smarty->assign('transform', $transform);

    $this->smarty->assign('title', 'Multiple delete label');
    $this->smarty->view('search/multiple_delete_label');
  }
  
  public function add_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css', 'operations.css');
    $this->use_autocomplete();
    $this->use_thickbox();
    $this->use_datepicker();
    $this->use_blockui();

    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      FORM_SCRIPT,
      GETPARAMS_SCRIPT,
      TIMERS_SCRIPT,
      'label_functions.js',
      'select_label.js',
      'add_multiple.js',
      'label_helpers.js',
      'taxonomy_functions.js',
      'common_sequence.js');
      
    $tree = $this->__get_search_term('post', 'encoded_tree', $encoded, $raw);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $mode = $this->get_parameter('mode');
    
    if($mode == 'add') {
      $this->smarty->assign('title', 'Multiple add label');
    } else if($mode == 'edit') {
      $this->smarty->assign('title', 'Multiple edit label');
    }
    
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);
    
    $this->smarty->assign('transform', $transform);
    $this->smarty->assign('mode', $mode);
    $this->smarty->assign('encoded', addmyslashes($encoded));
    $this->smarty->assign('encoded_no_slashes', $encoded);

    $this->smarty->view('search/multiple_add_label');
  }
  
  public function ref()
  {
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    $this->smarty->view_s('search/ref');
  }

  public function tax()
  {
    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view_s('search/tax');
  }
  
  public function get_histogram()
  {
    $search = $this->__get_search_term('post', 'encoded_tree');
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $type = $this->get_post('generate_histogram_type');
    
    $label_id = $this->get_post('histogram_label');
    $this->load->model('label_model');
    $label = $this->label_model->get($label_id);
    $this->smarty->assign('label', $label);
    
    $this->load->library('Plotter');
    
    if($this->plotter->make_distribution($search, $transform, $label_id, $type)) {
      $this->smarty->assign('hist_data', $this->plotter->get_js_data());
      $this->smarty->assign('total', $this->plotter->get_total());
      $this->smarty->assign('number_classes', $this->plotter->get_number_classes());
      $this->smarty->assign('mode', $this->plotter->get_mode());
      $this->smarty->assign('result', $this->plotter->get_result());
    
      switch($label['type']) {
        case 'integer':
        case 'float':
          $this->smarty->assign('min_class', $this->plotter->get_minimal_class());
          $this->smarty->assign('max_class', $this->plotter->get_maximal_class());
          $this->smarty->assign('average', $this->plotter->get_average());
          $this->smarty->assign('median', $this->plotter->get_median());
          break;
      }
      $this->smarty->assign('empty', false);
    } else {
      $this->smarty->assign('empty', true);
    }
    
    $this->smarty->view_s('search/histogram');
  }
  
  public function subsequences()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
    
    $search = $this->__get_search_term('post', 'encoded_tree', $encoded);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $label_id = $this->get_post('select_position');
    $keep = $this->get_post('keep_subsequence');
    
    $this->load->library('SubSequence');
    
    $this->load->model('label_model');
    $this->smarty->assign('label', $this->label_model->get($label_id));
    
    $this->use_mygrid();
    
    if($this->subsequence->generate($search, $transform, !$this->logged_in, $label_id, $keep ? TRUE : FALSE)) {
      $new_search = $this->subsequence->get_search_tree();
      $search_get = json_encode($new_search);
      $this->smarty->assign('encoded_sub', $search_get);
    } else {
      $this->smarty->assign('failure', true);
    }
    
    $this->smarty->assign('failed', $this->subsequence->get_failed());
    
    $this->smarty->assign('encoded_input', addmyslashes($encoded));
    $this->smarty->assign('transform_input', $transform);
    
    $this->smarty->assign('title', 'Generate sub sequences');
    $this->smarty->view('search/subsequences');
  }
  
  public function blast()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
      
    
    $this->smarty->assign('title', 'BLAST search');
    
    $search = $this->__get_search_term('post', 'encoded_tree', $encoded, $raw);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    
    $data = $this->search_model->get_search($search,
      array('start' => 0,
            'size' => 1,
            'transform' => $transform,
            'select' => 'id'));
    
    if(!$data) {
      $this->smarty->view('blast/no_data');
      return;
    }
    
    $this->load->model('sequence_model');
    $type = $this->sequence_model->get_type($data[0]['id']);
    
    $this->use_mygrid();
    $this->use_plusminus();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    
    $this->smarty->assign('tree_json', $raw);
    $this->smarty->assign('encoded', addmyslashes($encoded));
    $this->smarty->assign('encoded_no_slashes', $encoded);
    $this->smarty->assign('transform', $transform);
    $this->load->library('BlastLib');
    
    $programs = null;
    if($type == 'dna')
      $programs = array('blastn');
    else
      $programs = array('blastp', 'tblastx', 'blastp', 'tblastn');
    
    $this->smarty->assign('blast_programs', build_data_array($programs, 'id'));
    
    $this->smarty->assign('expect_values', BlastLib::$expect_values);
                                                 
    $this->smarty->assign('matrix', BlastLib::$matrix_values);
    
    $this->smarty->assign('query_genetic_codes', array(array('id' => 1, 'name' => 'Standard (1)'),
                                                 array('id' => 2, 'name' => 'Vertebrate Mitochondrial (2)'),
                                                 array('id' => 3, 'name' => 'Yeast Mitochondrial (3)'),
                                                 array('id' => 4, 'name' => 'Mold, Protozoan, and Coelocoel Mitochondrial (4)'),
                                                 array('id' => 5, 'name' => 'Invertebrate Mitochondrial (5)'),
                                                 array('id' => 6, 'name' => 'Ciliate Nuclear (6)'),
                                                 array('id' => 9, 'name' => 'Echinoderm Mitochondrial (9)'),
                                                 array('id' => 10, 'name' => 'Euplotid Nuclear (10)'),
                                                 array('id' => 11, 'name' => 'Bacterial (11)'),
                                                 array('id' => 12, 'name' => 'Alternative Yeast Nuclear (12)'),
                                                 array('id' => 13, 'name' => 'Ascidian Mitochondrial (13)'),
                                                 array('id' => 14, 'name' => 'Flatworm Mitochondrial (14)'),
                                                 array('id' => 15, 'name' => 'Blepharisma Macronuclear (15)')));
    
    $this->smarty->assign('db_genetic_codes', array(array('id' => 1, 'name' => 'Standard (1)'),
                                                    array('id' => 2, 'name' => 'Vertebrate Mitochondrial (2)'),
                                                    array('id' => 3, 'name' => 'Yeast Mitochondrial (3)'),
                                                    array('id' => 4, 'name' => 'Mold, Protozoan, and Coelocoel Mitochondrial (4)'),
                                                    array('id' => 5, 'name' => 'Invertebrate Mitochondrial (5)'),
                                                    array('id' => 6, 'name' => 'Ciliate Nuclear (6)'),
                                                    array('id' => 9, 'name' => 'Echinoderm Mitochondrial (9)'),
                                                    array('id' => 10, 'name' => 'Euplotid Nuclear (10)'),
                                                    array('id' => 11, 'name' => 'Bacterial (11)'),
                                                    array('id' => 12, 'name' => 'Alternative Yeast Nuclear (12)'),
                                                    array('id' => 13, 'name' => 'Ascidian Mitochondrial (13)'),
                                                    array('id' => 14, 'name' => 'Flatworm Mitochondrial (14)'),
                                                    array('id' => 15, 'name' => 'Blepharisma Macronuclear (15)')));
    
    $this->smarty->view('search/blast');
    
  }
}