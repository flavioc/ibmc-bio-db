<?php

class Sequence extends BioController
{
  function Sequence()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
  }

  public function browse()
  {
    $this->smarty->assign('title', 'Browse sequences');
    $this->smarty->load_scripts(VALIDATE_SCRIPT,
      'common_sequence.js');
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());

    $this->use_mygrid();

    $this->smarty->view('sequence/list');
  }

  public function get_export()
  {
    $tree_str = $this->get_post('tree');
    $tree = json_decode(stripslashes($tree_str), true);

    $transform = $this->__get_transform_label('transform', 'post');
    $seqs = $this->label_sequence_model->get_search($tree, null, null, array(), $transform, !$this->logged_in);
    
    $type = $this->get_post('format');

    if($type == 'fasta' || $type == 'xml') {
      $labels_str = $this->get_post('label_obj');
      $labels = json_decode(stripslashes($labels_str), true);

      return $this->__export_sequences_partial($seqs, $labels, array($tree, $transform), $type);
    } else {
      return $this->__export_sequences($seqs, $type, '');
    }
  }

  public function export_search()
  {
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

    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $this->smarty->assign('transform', $transform);
    $seqs = $this->label_sequence_model->get_search($tree, null, null, array(), $transform, !$this->logged_in);
    
    $all = $this->label_sequence_model->get_all_labels($seqs);
    $this->smarty->assign('labels', $all);

    $this->smarty->view('sequence/export_search');
  }

  public function search()
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

  public function search_tax()
  {
    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view_s('sequence/search_tax');
  }

  public function search_ref()
  {
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());
    $this->smarty->view_s('sequence/search_ref');
  }

  private function __get_search_term()
  {
    $search = stripslashes($this->get_parameter('search'));
    $search_term = null;
    
    if($search) {
      $search_term = json_decode($search, true);
    }

    return $search_term;
  }

  public function humanize_search()
  {
    $tree = $this->__get_search_term();
    $tree_str = search_tree_to_string($tree, '<span class="compound-operator">', '</span>');

    echo $tree_str;
  }

  public function get_search()
  {
    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');
    $search = $this->__get_search_term();
    $transform = $this->__get_transform_label();

    $ordering_name = $this->get_order('name');
    $ordering_update = $this->get_order('update');
    $ordering_user = $this->get_order('user_name');

    $this->json_return($this->label_sequence_model->get_search($search,
      $start, $size,
      array('name' => $ordering_name,
            'update' => $ordering_update,
            'user_name' => $ordering_user),
            $transform,
            !$this->logged_in));
  }

  public function get_search_total()
  {
    $search = $this->__get_search_term();
    $transform = $this->__get_transform_label();

    $this->json_return(
      $this->label_sequence_model->get_search_total($search, $transform, !$this->logged_in));
  }

  public function multiple_add_label()
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
  
  public function multiple_delete_label()
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

  public function get_all()
  {
    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    $ordering_name = $this->get_order('name');
    $ordering_update = $this->get_order('update');
    $ordering_user = $this->get_order('user_name');

    $filter_name = $this->get_parameter('name');
    $filter_user = $this->get_parameter('user');

    $this->json_return($this->sequence_model->get_all($start, $size,
      array('name' => $filter_name,
            'user' => $filter_user,
            'only_public' => !$this->logged_in),
      array('name' => $ordering_name,
            'update' => $ordering_update,
            'user_name' => $ordering_user)));
  }

  public function get_total()
  {
    $filter_name = $this->get_parameter('name');
    $filter_user = $this->get_parameter('user');

    $this->json_return(
      $this->sequence_model->get_total(
        array('name' => $filter_name,
              'user' => $filter_user,
              'only_public' => !$this->logged_in)));
  }

  private function __invalid_sequence($id)
  {
    $this->smarty->assign('title', 'Invalid sequence');
    $this->smarty->assign('id', $id);
    $this->smarty->view('sequence/invalid_sequence');
  }

  public function labels($id = null)
  {
    if(!$id) {
      $id = $this->get_parameter('id');
    }

    if(!$this->__can_access($id)) {
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
    $this->use_datepicker();
    $this->use_blockui();

    $this->__load_sequence($id);
    $this->smarty->assign('missing',
      $this->label_sequence_model->has_missing($id));
    $this->smarty->assign('bad_multiple',
      $this->label_sequence_model->has_bad_multiple($id));

    $this->smarty->view('sequence/view_labels');
  }

  private function __load_js()
  {
    $this->smarty->load_scripts(JSON_SCRIPT, VALIDATE_SCRIPT,
      AUTOCOMPLETE_SCRIPT, FORM_SCRIPT, JEDITABLE_SCRIPT,
      'sequence_functions.js', 'taxonomy_functions.js',
      'common_sequence.js', 'label_helpers.js');
  }

  public function view($id)
  {
    if(!$this->__can_access($id)) {
      $this->set_error_message('Sequence is private, please login');
      redirect('sequence/browse');
      return;
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

  private function __load_sequence($id)
  {
    $sequence = $this->sequence_model->get($id);
    $sequence['content'] = sequence_short_content($sequence['content']);
    $this->smarty->assign('sequence', $sequence);
    
    $trans_id = $this->sequence_model->get_translated_sequence($id);
    if($trans_id) {
      $trans_sequence = $this->sequence_model->get($trans_id);
      $this->smarty->assign('trans_sequence', $trans_sequence);
    }
  }

  public function add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->fetch_form_row('file');
    $this->smarty->fetch_form_row('file2');
    
    $this->smarty->assign('title', 'Add batch sequences');
    $this->smarty->view('sequence/add_batch');
  }

  private function __get_sequence_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'txt|application/octet-stream|exe|xml';

    return $config;
  }
  
  private function __get_sequence_upload($name)
  {
    $upload_ret = $this->upload->do_upload($name);

    if($upload_ret) {
      $data = $this->upload->data();
      return $data['full_path'];
    }
    
    return null;
  }

  public function do_add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->load->library('upload', $this->__get_sequence_upload_config());
    $option = $this->get_post('upload_option');
    $is_duo = ($option == 'duo');
    $is_generate = ($option == 'generate');
    $to_link = ($is_duo || $is_generate);
    
    $file1 = $this->__get_sequence_upload('file');
    if(!$file1) {
      $this->set_upload_form_error('file');
      redirect('sequence/add_batch');
      return;
    }
    
    if($is_duo) {
      $file2 = $this->__get_sequence_upload('file2');
      if(!$file2) {
        unlink($file1);
        $this->set_upload_form_error('file2');
        redirect('sequence/add_batch');
        return;
      }
      
      if($file1 == $file2) {
        unlink($file1);
        $this->set_form_error('file', 'The files are the same');
        redirect('sequence/add_batch');
        return;
      }
    }
    
    $this->load->model('label_model');
    $this->load->model('taxonomy_model');
    $this->load->library('ImportInfo');
    $this->load->helper('xml_importer');
    $this->load->helper('fasta_importer');
    $this->load->helper('seq_importer');
      
    $this->load->helper('search');
    
    $info1 = import_sequence_file($this, $file1);
    unlink($file1);
    
    if($to_link) {
      if(!$info1->all_dna()) {
        if($is_duo) {
          unlink($file2);
        }
        $this->set_form_error('file', 'All sequences should be DNA sequences');
        redirect('sequence/add_batch');
        return;
      }
    }
    
    if($is_generate) {
      $is_duo = true;
      $file2 = $info1->convert_protein_file();
      
      if(!$file2) {
        $this->set_form_error('file', 'Error generating protein file');
        redirect('sequence/add_batch');
        return;
      }
    }
    
    if($is_duo) {
      $info2 = import_sequence_file($this, $file2);
      unlink($file2);
      
      if(!$info2->all_protein()) {
        $this->set_form_error('file', 'All sequences must be protein sequences');
        redirect('sequence/add_batch');
        return;
      }
    } else {
      $info2 = null;
    }
    
    if(!$info1 || ($is_duo && !$info2)) {
      if(!$info1) {
        $this->set_form_error('file', 'Error reading file');
      }
      if($is_duo && !$info2) {
        $this->set_form_error('file2', 'Error reading file');
      }
      redirect('sequence/add_batch');
      return;
    }
    
    if($is_duo) {
      if(!$info1->duo_match($info2)) {
        $this->set_form_error('file', "Sequence files must have the same number of sequences");
        redirect('sequence/add_batch');
        return;
      }
    }
    
    list($seqs, $labels) = $info1->import();
  
    $this->smarty->assign('sequences', $seqs);
    $this->smarty->assign('labels', $labels);
    $search_tree1 = get_search_tree_sequences($seqs);
    $search_tree_get1 = rawurlencode(json_encode($search_tree1));
    $this->smarty->assign('search_tree_get1', $search_tree_get1);
    $this->smarty->assign('is_duo', $is_duo);
    
    if($is_duo) {
      list($seqs2, $labels2) = $info2->import();
      
      $this->smarty->assign('sequences2', $seqs2);
      $this->smarty->assign('labels2', $labels2);
      $search_tree2 = get_search_tree_sequences($seqs2);
      $search_tree_get2 = rawurlencode(json_encode($search_tree2));
      $this->smarty->assign('search_tree_get2', $search_tree_get2);
      
      $info1->link_sequences($info2);
    }
  
    $this->smarty->assign('title', 'Batch import');
    $this->smarty->view('sequence/batch_report');
  }

  public function add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->smarty->assign('title', 'Add sequence');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('content');

    $this->smarty->view('sequence/add');
  }

  public function do_add()
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

      if($this->sequence_model->has_same_sequence($name, $content)) {
        $id = $this->sequence_model->get_id_by_name_and_content($name, $content);
      } else {
        $id = $this->sequence_model->add($name, $content);
      }

      $protein = $this->get_post('protein');
      if($protein) {
        $this->load->library('ImportInfo');
        $info = new ImportInfo($this);
        $info->add_sequence($name, $content, $id);
        
        if($info->all_dna()) {
          $file = $info->convert_protein_file();
          $this->load->helper('fasta_importer');
          
          $info2 = import_fasta_file($this, $file);
          unlink($file);
          
          $info2->import();
          $info->link_sequences($info2);
        }
      }
      
      redirect("sequence/view/$id");
    }
  }
  
  private function __can_access($id)
  {
    return $this->logged_in || $this->sequence_model->permission_public($id);
  }

  public function download($id)
  {
    if(!$this->__can_access($id)) {
      return $this->invalid_permission();
    }
    
    echo $this->sequence_model->get_content($id);
  }

  public function fetch($id)
  {
    if(!$this->__can_access($id)) {
      return $this->invalid_permission();
    }
    
    $content = $this->sequence_model->get_content($id);

    echo sequence_split($content);
  }

  public function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');
    $this->sequence_model->delete($id);

    redirect('sequence/browse');
  }

  public function delete_dialog($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    $sequence = $this->sequence_model->get($id);
    $this->smarty->assign('sequence', $sequence);

    $this->smarty->view_s('sequence/delete');
  }

  public function edit_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('seq');
    $value = $this->get_post('value');

    $this->sequence_model->edit_name($id, $value);

    echo $this->sequence_model->get_name($id);
  }

  public function edit_content()
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

  public function export()
  {
    $id = $this->get_post('id');
    
    if(!$this->__can_access($id)) {
      return $this->invalid_permission();
    }

    if(!$this->sequence_model->has_sequence($id)) {
      return;
    }
    
    $type = $this->get_post('format');
    $seqs = array($this->sequence_model->get($id));

    return $this->__export_sequences($seqs, $type, "sequence id $id");
  }

  public function export_all()
  { 
    $filter_name = $this->get_post('export_name');
    $filter_user = $this->get_post('export_user');

    $type = $this->get_post('format');
    
    if(!$filter_user && !$filter_name) {
      $comment = "all sequences";
    } else {
      $comment = '';
      if($filter_name) {
        $comment = "filter name $filter_name";
      }
      
      if($filter_user) {
        if($comment) {
          $comment .= ' ';
        }
        $this->load->model('user_model');
        $user = $this->user_model->get_name($filter_user);
        $comment .= "by user $user";
      }
    }
    
    return $this->__export_sequences(
      $this->sequence_model->get_all(null, null,
                    array('name' => $filter_name,
                          'user' => $filter_user,
                          'only_public' => !$this->logged_in)),
                $type, $comment);
  }

  private function __export_sequences_partial($sequences, $labels_id, $data, $type)
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

    list($tree, $transform) = $data;
    $tree_str = search_tree_to_string($tree);
    
    if($transform) {
      $this->load->model('label_model');
      $trans_name = $this->label_model->get_name($transform);
    } else {
      $trans_name = null;
    }
    
    if($type == 'fasta') {
      $comment = " - $tree_str";
      if($trans_name) {
        $comment = "$comment - transformed by label $trans_name";
      }
      $this->__do_export_fasta($sequences, $seq_labels, $comment);
    } else {
      $comment = $tree_str;
      if($trans_name) {
        $comment = "$comment ; transformed by label $trans_name";
      }
      $this->__do_export_xml($sequences, $seq_labels, $comment);
    }
  }

  private function __export_sequences($sequences, $type, $comment)
  {
    foreach($sequences as &$seq) {
      $id = $seq['id'];
      if(!array_key_exists('content', $seq)) {
        $seq['content'] = $this->sequence_model->get_content($id);
      }
    }
    
    if($type == 'fasta' || $type == 'xml') {
      $seq_labels = array();

      foreach($sequences as &$seq) {
        $id = $seq['id'];
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

  private function __do_export_others($sequences, $type)
  {
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"sequences.$type\"");
    
    echo export_sequences_others($sequences, $type);
  }

  private function __do_export_fasta($sequences, $seq_labels, $extra_comments = '')
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.fasta"');
    
    echo export_sequences_fasta($sequences, $seq_labels,
      $this->__get_basic_comments() . " $extra_comments");
  }

  private function __do_export_xml($sequences, $seq_labels, $comment)
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.xml"');
    
    echo export_sequences_xml($sequences, $seq_labels, $this->username, $comment);
  }

  private function __get_basic_comments()
  {
    return $this->username . ' - ' . timestamp_string();
  }
}