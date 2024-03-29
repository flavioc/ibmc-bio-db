<?php

class Sequence extends BioController
{
  private $event = null;
  
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
    $max_position = (int)$start + (int)$length - 1;
    
    if($max_position > $seq_length) {
      $this->smarty->assign('invalid', true);
      $this->smarty->assign('actual_length', $seq_length);
    } else {
      $segment = $this->sequence_model->get_content_segment($id, $start, $length);
      $this->smarty->assign('segment', sequence_split($segment, "<br />"));
      $this->smarty->assign('invalid', false);
      
      // check if there is a subsequence with this content
      $sub_name = build_sub_sequence_name($this->sequence_model->get_name($id), $start, $length);
      $sub_id = $this->sequence_model->get_id_by_name_and_content($sub_name, $segment);
      
      if($sub_id)
        $this->smarty->assign('sub_id', $sub_id);
    }
    
    $this->smarty->view_s('sequence/view_position');
  }
  
  /*
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
  */

  public function get_all()
  {
    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');
    $disable_ordering = $this->get_parameter('disable_ordering');

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
            'user_name' => $ordering_user),
            'id, name, update_user_id, `update`, user_name',
            !$disable_ordering));
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
    
    $sequence = $this->sequence_model->get($id);

    $this->smarty->assign('title', 'Sequence/Labels: '.$sequence['name']);
    $this->__load_js();
    $this->use_thickbox();
    $this->use_mygrid();
    $this->use_plusminus();
    $this->use_datepicker();
    $this->use_blockui();
    $this->smarty->load_stylesheets('labels.css');
    
    $this->assign_label_types();
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());

    $this->__load_sequence($sequence);
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
      redirect('welcome');
      return;
    }

    if(!$this->sequence_model->has_id($id)) {
      $this->__invalid_sequence($id);
      return;
    }

    $sequence = $this->sequence_model->get($id);
    
    $this->smarty->assign('id', $id);
    $this->smarty->assign('title', 'View '.$sequence['name']);
    $this->__load_js();
    $this->use_impromptu();
    $this->__load_sequence($sequence);
    $this->smarty->assign('immutable', $this->sequence_model->is_immutable($id));

    $this->smarty->view('sequence/view');
  }

  private function __load_sequence($sequence)
  {
    $id = $sequence['id'];
    
    $sequence['content'] = sequence_short_content($sequence['content']);
    $this->smarty->assign('sequence', $sequence);
    
    $trans_id = $this->sequence_model->get_translated_sequence($id);
    if($trans_id) {
      $trans_sequence = $this->sequence_model->get($trans_id);
      $this->smarty->assign('trans_sequence', $trans_sequence);
    }
    
    $super_id = $this->sequence_model->get_super($id);
    if($super_id) {
      $super = $this->sequence_model->get($super_id);
      $this->smarty->assign('super', $super);
    }
    
    $lifetime = $this->sequence_model->get_lifetime($id);
    if($lifetime)
      $this->smarty->assign('lifetime', $lifetime);
  }

  public function add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->load_scripts(TIMERS_SCRIPT, FORM_SCRIPT);
    $this->use_blockui();
    
    $this->smarty->fetch_form_row('file');
    $this->smarty->fetch_form_row('file2');
    
    $this->smarty->assign('event', generate_random());
    
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
  
  private function __show_batch_report()
  {
    $this->use_mygrid();
    $this->use_thickbox();
    
    $this->smarty->assign('title', 'Batch import');
    $this->smarty->view('sequence/batch_report');
  }
  
  private function __batch_init()
  {
    $this->load->library('upload', $this->__get_sequence_upload_config());
    $this->load->library('SequenceImporter');
    $this->load->library('SeqSearchTree');
  }
  
  private function __batch_import_and_show($info, $n)
  {
    list($seqs, $labels) = $info->import();
  
    $this->smarty->assign("sequences$n", $seqs);
    $this->smarty->assign("labels$n", $labels);
    $this->smarty->assign("empty$n", $info->get_empty_sequences());
    $this->smarty->assign("error$n", $info->get_error_lines());
    $search_tree = $this->seqsearchtree->get_tree($seqs);
    $search_tree_get = json_encode($search_tree);
    $this->smarty->assign("search_tree_get$n", $search_tree_get);
  }
  
  private function __batch_go_back()
  {
    if($this->event) {
      $this->event_model->remove($this->event);
      $this->event = null;
    }
    redirect('sequence/add_batch');
  }
  
  private function __do_add_batch_none()
  {
    if($this->event)
      $event_data = array('file' => array(), 'event' => $this->event);
    else
      $event_data = null;
    
    $this->__batch_init();
    
    $file = $this->__get_sequence_upload('file');
    if(!$file) {
      $this->set_upload_form_error('file');
      $this->__batch_go_back();
      return;
    }
    
    $info = $this->sequenceimporter->import_file($file, $event_data, 'file');
    unlink($file);
    
    if(!$info) {
      $this->set_form_error('file', 'Error reading file');
      $this->__batch_go_back();
      return;
    }
    
    $this->__batch_import_and_show($info, 1);
    $this->__show_batch_report();
  }
  
  private function __do_add_batch_generate()
  {
    if($this->event)
      $event_data = array('generate_file' => array(), 'generated_file' => array(), 'event' => $this->event);
    else
      $event_data = null;
      
    $this->__batch_init();
    
    $file1 = $this->__get_sequence_upload('file');
    
    if(!$file1) {
      $this->set_upload_form_error('file');
      $this->__batch_go_back();
      return;
    }
    
    $keep_structure = $this->get_post('keep_structure') && !is_xml_file($file1);
    
    $info1 = $this->sequenceimporter->import_file($file1, $event_data, 'generate_file');
    
    if(!$info1) {
      unlink($file1);
      $this->set_form_error('file', 'Error reading file');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info1->all_dna()) {
      unlink($file1);
      $this->set_form_error('file', 'All sequences should be DNA sequences');
      $this->__batch_go_back();
      return;
    }
    
    $file2 = null;
    if($keep_structure) {
      $this->load->library('SequenceConverter');
      $file2 = $this->sequenceconverter->convert_dna_protein($file1);
    } else {
      $file2 = $info1->convert_protein_file();
    }
    unlink($file1);
    
    if(!$file2) {
      $this->set_form_error('file', 'Error generating protein file');
      $this->__batch_go_back();
      return;
    }
    
    $info2 = $this->sequenceimporter->import_file($file2, $event_data, 'generated_file');
    unlink($file2);
    
    if(!$info2) {
      $this->set_form_error('file2', 'Error reading file');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info2->all_protein()) {
      $this->set_form_error('file', 'All sequences must be protein sequences');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info1->duo_match($info2)) {
      $this->set_form_error('file', 'Sequence files must have the same number of sequences');
      $this->__batch_go_back();
      return;
    }
    
    $this->__batch_import_and_show($info1, 1);
    $this->__batch_import_and_show($info2, 2);
    
    $info1->link_sequences($info2);
    
    $this->__show_batch_report();
  }
  
  private function __do_add_batch_duo()
  {
    if($this->event)
      $event_data = array('file1' => array(), 'file2' => array(), 'event' => $this->event);
    else
      $event_data = null;
      
    $this->__batch_init();
    
    $file1 = $this->__get_sequence_upload('file');
    $file2 = $this->__get_sequence_upload('file2');
    
    if(!$file1 || !$file2) {
      if($file1)
        unlink($file1);
      if($file2)
        unlink($file2);
      if(!$file1)
        $this->set_upload_form_error('file');
      if(!$file2)
        $this->set_upload_form_error('file2');
      $this->__batch_go_back();
      return;
    }
    
    $info1 = $this->sequenceimporter->import_file($file1, $event_data, 'file1');
    unlink($file1);
    
    if(!$info1) {
      unlink($file2);
      $this->set_form_error('file', 'Error reading file');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info1->all_dna()) {
      unlink($file2);
      $this->set_form_error('file', 'All sequences should be DNA sequences');
      $this->__batch_go_back();
      return;
    }
    
    $info2 = $this->sequenceimporter->import_file($file2, $event_data, 'file2');
    unlink($file2);
    
    if(!$info2) {
      $this->set_form_error('file2', 'Error reading file');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info2->all_protein()) {
      $this->set_form_error('file', 'All sequences must be protein sequences');
      $this->__batch_go_back();
      return;
    }
    
    if(!$info1->duo_match($info2)) {
      $this->set_form_error('file', "Sequence files must have the same number of sequences");
      $this->__batch_go_back();
      return;
    }
  
    $this->__batch_import_and_show($info1, 1);
    $this->__batch_import_and_show($info2, 2);
      
    $info1->link_sequences($info2);
  
    $this->__show_batch_report();
  }

  public function do_add_batch()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->more_time_limit();
    
    $option = $this->get_post('upload_option');
    $this->event = $this->get_post('event');
    
    if($this->event) {
      $this->load->model('event_model');
      $this->event_model->remove($this->event);
      $this->event_model->add($this->event);
    }
    
    switch($option) {
      case 'none':
        $ret = $this->__do_add_batch_none();
        break;
      case 'duo':
        $ret = $this->__do_add_batch_duo();
        break;
      case 'generate':
        $ret = $this->__do_add_batch_generate();
        break;
      default:
        $this->set_form_error('file', 'Upload limits reached, please contact the site administrator');
        $this->__batch_go_back();
        return false;
    }
    
    if($this->event)
      $this->event_model->remove($this->event);
  
    return $ret;
  }

  public function add()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
    
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

      $id = $this->sequence_model->add($name, $content);

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
      
      redirect("sequence/labels/$id");
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
      return $this->invalid_permission_nothing();
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
    $this->set_info_message("Sequence has been deleted.");

    redirect('welcome');
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
