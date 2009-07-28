<?php

class Sequence extends BioController
{
  function Sequence() {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
  }

  function browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Browse sequences');
    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      'common_sequence.js');
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());

    $this->use_mygrid();

    $this->smarty->view('sequence/list');
  }

  function get_export()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $tree_str = $this->get_post('tree');
    $tree = json_decode(stripslashes($tree_str), true);

    $seqs = $this->label_sequence_model->get_search($tree);
    
    $type = $this->get_post('format');

    if($type == 'fasta' || $type == 'xml') {
      $labels_str = $this->get_post('label_obj');
      $labels = json_decode(stripslashes($labels_str), true);

      return $this->export_sequences_partial($seqs, $labels, $tree, $type);
    } else {
      $ids = array();
      foreach($seqs as &$seq) {
        $ids[] = $seq['id'];
      }
      return $this->export_sequences($ids, $type);
    }
  }

  function export_search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Export search');
    $this->smarty->load_stylesheets('export.css');

    $encoded = $this->get_post('encoded_tree');

    $json = stripslashes($encoded);
    $tree = json_decode($json, true);
    $this->smarty->assign('tree_json', $json);
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);
    $tree_html = search_tree_to_html($tree);
    $this->smarty->assign('tree_html', $tree_html);

    $seqs = $this->label_sequence_model->get_search($tree);
    $all = $this->label_sequence_model->get_all_labels($seqs);
    $this->smarty->assign('labels', $all);

    $this->smarty->view('sequence/export_search');
  }

  function search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Search sequences');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'label_functions.js',
      'sequence_search.js', SELECTBOXES_SCRIPT, 'taxonomy_functions.js',
      'common_sequence.js', GETPARAMS_SCRIPT);
    $this->smarty->load_stylesheets('search.css');
    $this->use_mygrid();
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    $this->assign_label_types(true);
    $this->use_autocomplete();
    $this->use_thickbox();
    $this->use_livequery();

    $this->smarty->assign('type', $this->get_parameter('type'));
    $this->smarty->view('sequence/search');
  }

  function search_tax()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view_s('sequence/search_tax');
  }

  function search_ref()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    $this->smarty->view_s('sequence/search_ref');
  }

  function __get_search_term()
  {
    $search = stripslashes($this->get_parameter('search'));
    $search_term = null;
    if($search) {
      $search_term = json_decode($search, true);
    }

    return $search_term;
  }
  
  function humanize_search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }
    
    $tree = $this->__get_search_term();
    $tree_str = search_tree_to_string($tree);
    
    echo $tree_str;
  }

  function get_search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');
    $search = $this->__get_search_term();

    $ordering_name = $this->get_order('name');
    $ordering_update = $this->get_order('update');
    $ordering_user = $this->get_order('user_name');

    $this->json_return($this->label_sequence_model->get_search($search,
      $start, $size,
      array('name' => $ordering_name,
            'update' => $ordering_update,
            'user_name' => $ordering_user)));
  }

  function get_search_total()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $search = $this->__get_search_term();

    $this->json_return(
      $this->label_sequence_model->get_search_total($search));
  }

  function multiple_add_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css');
    $this->use_autocomplete();
    $this->use_thickbox();
    $this->use_livequery();

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
    $mode = $this->get_parameter('mode');
    
    if($mode == 'add') {
      $this->smarty->assign('title', 'Multiple add label');
    } else if($mode == 'edit') {
      $this->smarty->assign('title', 'Multiple edit label');
    }
    
    $this->smarty->assign('mode', $mode);
    $this->smarty->assign('encoded', $encoded);

    $this->smarty->view('sequence/multiple_add_label');
  }
  
  function multiple_delete_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_stylesheets('add_label.css');
    $this->use_autocomplete();
    $this->use_impromptu();

    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      FORM_SCRIPT,
      CONFIRM_SCRIPT,
      'label_functions.js',
      'select_label.js',
      'common_sequence.js',
      'delete_multiple.js');

    $encoded = $this->get_post('encoded_tree');
    $this->smarty->assign('encoded', $encoded);
    
    $this->smarty->assign('title', 'Multiple delete label');
    $this->smarty->view('sequence/multiple_delete_label');
  }

  function get_all()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    $ordering_name = $this->get_order('name');
    $ordering_update = $this->get_order('update');
    $ordering_user = $this->get_order('user_name');

    $filter_name = $this->get_parameter('name');
    $filter_user = $this->get_parameter('user');

    $this->json_return($this->sequence_model->get_all($start, $size,
      array('name' => $filter_name,
            'user' => $filter_user),
      array('name' => $ordering_name,
            'update' => $ordering_update,
            'user_name' => $ordering_user)));
  }

  function get_total()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $filter_name = $this->get_parameter('name');
    $filter_user = $this->get_parameter('user');

    $this->json_return(
      $this->sequence_model->get_total(
        array('name' => $filter_name,
              'user' => $filter_user)));
  }

  function __invalid_sequence($id)
  {
    $this->smarty->assign('title', 'Invalid sequence');
    $this->smarty->assign('id', $id);
    $this->smarty->view('sequence/invalid_sequence');
  }

  function labels($id = null)
  {
    if(!$id) {
      $id = $this->get_parameter('id');
    }

    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission();
    }

    if(!$this->sequence_model->has_id($id)) {
      $this->__invalid_sequence($id);
      return;
    }

    $this->smarty->assign('title', 'View labels');
    $this->__load_js();
    $this->use_thickbox();
    $this->use_mygrid();
    $this->use_plusminus();
    $this->use_livequery();

    $this->__load_sequence($id);
    $this->smarty->assign('missing',
      $this->label_sequence_model->has_missing($id));
    $this->smarty->assign('bad_multiple',
      $this->label_sequence_model->has_bad_multiple($id));

    $this->smarty->view('sequence/view_labels');
  }

  function __load_js()
  {
    $this->smarty->load_scripts(JSON_SCRIPT, VALIDATE_SCRIPT,
      AUTOCOMPLETE_SCRIPT, FORM_SCRIPT, JEDITABLE_SCRIPT,
      'sequence_functions.js', 'taxonomy_functions.js',
      'common_sequence.js', 'label_helpers.js');
  }

  function view($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission();
    }

    if(!$this->sequence_model->has_id($id)) {
      $this->__invalid_sequence($id);
      return;
    }

    $this->smarty->assign('id', $id);
    $this->smarty->assign('title', 'View sequence');
    $this->__load_js();
    $this->use_impromptu();
    $this->__load_sequence($id);

    $this->smarty->view('sequence/view');
  }

  function __load_sequence($id)
  {
    $sequence = $this->sequence_model->get($id);
    $sequence['content'] = sequence_short_content($sequence['content']);
    $this->smarty->assign('sequence', $sequence);
  }

  function add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->fetch_form_row('file');

    $this->smarty->assign('title', 'Add batch sequences');
    $this->smarty->view('sequence/add_batch');
  }

  function __get_fasta_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'txt|application/octet-stream|exe';

    return $config;
  }

  function __import_fasta_file($file)
  {
    $seqs_labels = import_fasta_file($this, $file);
    $seqs = $seqs_labels[0];
    $labels = $seqs_labels[1];

    foreach($seqs as &$seq)
    {
      $seq['short_content'] = sequence_short_content($seq['data']['content']);
    }

    $this->smarty->assign('sequences', $seqs);
    $this->smarty->assign('labels', $labels);

    $this->smarty->assign('title', 'Batch results');
    $this->smarty->view('sequence/batch_report');
  }

  function do_add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->load->library('upload', $this->__get_fasta_upload_config());

    $upload_ret = $this->upload->do_upload('file');

    if($upload_ret) {
      $data = $this->upload->data();
      $this->load->model('label_model');
      $this->load->model('taxonomy_model');
      $this->__import_fasta_file($data['full_path']);
    } else {
      $this->set_upload_form_error('file');
      redirect('sequence/add_batch');
    }
  }

  function add()
  {
    $this->smarty->assign('title', 'Add sequence');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('content');

    $this->smarty->view('sequence/add');
  }

  function do_add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $errors = false;

    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[255]');
    $this->form_validation->set_rules('content', 'Content', 'trim|required|max_length[65535]');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('content');

      redirect('sequence/add');
    } else {
      $name = $this->get_post('name');
      $content = $this->get_post('content');

      $id = $this->sequence_model->add($name, $content);

      redirect("sequence/view/$id");
    }
  }

  function download($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    echo $this->sequence_model->get_content($id);
  }

  function fetch($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    $content = $this->sequence_model->get_content($id);

    echo sequence_split($content);
  }

  function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');
    $this->sequence_model->delete($id);

    redirect('sequence/browse');
  }

  function delete_dialog($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    $sequence = $this->sequence_model->get($id);
    $this->smarty->assign('sequence', $sequence);

    $this->smarty->view_s('sequence/delete');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('seq');
    $value = $this->get_post('value');

    $this->sequence_model->edit_name($id, $value);

    echo $this->sequence_model->get_name($id);
  }

  function edit_content()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('seq');
    $value = $this->get_post('value');
    
    if($value == '') {
      return;
    }

    $value = sequence_join($value);

    $this->sequence_model->edit_content($id, $value);

    echo sequence_short_content($value) . "...";
  }

  function export()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $id = $this->get_post('id');

    if(!$this->sequence_model->has_sequence($id)) {
      return;
    }
    
    $type = $this->get_post('format');

    return $this->export_sequences(array($id), $type, "sequence id $id");
  }

  function export_all()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $type = $this->get_post('format');
    $ids = $this->sequence_model->get_ids_array();
    return $this->export_sequences($ids, $type, "all sequences");
  }

  function export_sequences_partial($sequences, $labels_id, $tree, $type)
  {
    $seq_labels = array();

    foreach($sequences as &$seq)
    {
      // get sequence content
      $seq = $this->sequence_model->get($seq['id']);

      // try to get the label ids for this sequence
      $labels = array();
      foreach($labels_id as $label_id) {
        $new = $this->label_sequence_model->get_label_ids($seq['id'], $label_id);
        if($new != null) {
          $labels[] = $new;
        }
      }
      $seq_labels[] = $labels;
    }

    $tree_str = search_tree_to_string($tree);
    if($type == 'fasta') {
      $this->__do_export_fasta($sequences, $seq_labels, "- $tree_str");
    } else {
      $this->__do_export_xml($sequences, $seq_labels, $tree_str);
    }
  }

  function export_sequences($sequences_id, $type, $comment)
  {
    $sequences = array();
    foreach($sequences_id as $id) {
      $sequences[] = $this->sequence_model->get($id);
    }
    
    if($type == 'fasta' || $type == 'xml') {
      $seq_labels = array();

      foreach($sequences_id as $id) {
        $seq_labels[] = $this->label_sequence_model->get_sequence($id);
      }

      if($type == 'fasta') {
        $this->__do_export_fasta($sequences, $seq_labels, "- $comment");
      } else {
        $this->__do_export_xml($sequences, $seq_labels, $comment);
      }
    } else {
      $this->__do_export_others($sequences, $type);
    }
  }

  function __do_export_others($sequences, $type)
  {
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"sequences.$type\"");
    
    echo export_sequences_others($sequences, $type);
  }
  
  function __do_export_fasta($sequences, $seq_labels, $extra_comments = '')
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.fasta"');
    
    echo export_sequences_fasta($sequences, $seq_labels,
      $this->__get_basic_comments() . " $extra_comments");
  }
  
  function __do_export_xml($sequences, $seq_labels, $comment)
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.xml"');
    
    echo export_sequences_xml($sequences, $seq_labels, $this->username, $comment);
  }

  function __get_basic_comments()
  {
    return $this->username . ' - ' . timestamp_string();
  }
}

