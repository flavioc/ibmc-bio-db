<?php

class Sequence extends BioController
{
  function Sequence()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
  }
  
  public function get_position_content()
  {
    $id = $this->get_parameter('id');
    
    if(!$this->__can_access($id)) {
      return $this->invalid_permission();
    }
    
    $start = $this->get_parameter('start');
    $length = $this->get_parameter('length');
    
    $this->smarty->assign('start', $start);
    $this->smarty->assign('length', $length);
    
    $seq_length = $this->sequence_model->get_content_length($id);
    $max_position = (int)$start + (int)$length;
    
    if($max_position > $seq_length) {
      $this->smarty->assign('invalid', true);
      $this->smarty->assign('actual_length', $seq_length);
    } else {
      $segment = $this->sequence_model->get_content_segment($id, $start, $length);
      $this->smarty->assign('segment', sequence_split($segment, "<br />"));
      $this->smarty->assign('invalid', false);
    }
    
    $this->smarty->view_s('sequence/view_position');
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
    $this->smarty->load_stylesheets('labels.css');

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

    $this->load->library('SequenceImporter');
    $this->load->library('SeqSearchTree');
    
    $info1 = $this->sequenceimporter->import_file($file1);
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
      $info2 = $this->sequenceimporter->import_file($file2);
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
    $search_tree1 = $this->seqsearchtree->get_tree($seqs);
    $search_tree_get1 = json_encode($search_tree1);
    $this->smarty->assign('search_tree_get1', $search_tree_get1);
    $this->smarty->assign('is_duo', $is_duo);
    
    if($is_duo) {
      list($seqs2, $labels2) = $info2->import();
      
      $this->smarty->assign('sequences2', $seqs2);
      $this->smarty->assign('labels2', $labels2);
      $search_tree2 = $this->seqsearchtree->get_tree($seqs2);
      $search_tree_get2 = json_encode($search_tree2);
      $this->smarty->assign('search_tree_get2', $search_tree_get2);
      
      $info1->link_sequences($info2);
    }
  
    $this->smarty->load_stylesheets(MYGRID_THEME);
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
        $this->load->plugin('import_info');
        $info = new ImportInfo();
        $info->add_sequence($name, $content, $id);
        
        if($info->all_dna()) {
          $file = $info->convert_protein_file();
          
          $this->load->library('SequenceImporter');
          
          $info2 = $this->sequenceimporter->import_fasta($file);
          
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
}