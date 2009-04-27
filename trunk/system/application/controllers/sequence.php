<?php

class Sequence extends BioController
{
  function Sequence() {
    parent::BioController();
    $this->load->model('sequence_model');
  }

  function browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Browse sequences');

    $this->use_mygrid();

    $this->smarty->view('sequence/list');
  }

  function get_all()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    $this->json_return($this->sequence_model->get_all($start, $size));
  }

  function get_total()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $this->json_return($this->sequence_model->get_total());
  }

  function view($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission();
    }

    if(!$this->sequence_model->has_id($id)) {
      $this->smarty->assign('title', 'Invalid sequence');
      $this->smarty->assign('id', $id);
      $this->smarty->view('sequence/invalid_sequence');
      return;
    }

    $this->smarty->assign('title', 'View sequence');
    $this->smarty->load_scripts(JSON_SCRIPT, VALIDATE_SCRIPT,
      AUTOCOMPLETE_SCRIPT, FORM_SCRIPT,
      'sequence_functions.js', 'taxonomy_functions.js');
    $this->use_thickbox();
    $this->use_mygrid();
    $this->use_plusminus();

    $this->load->model('label_sequence_model');

    $sequence = $this->sequence_model->get($id);
    $sequence['content'] = sequence_short_content($sequence['content']);

    $this->smarty->assign('sequence', $sequence);
    $this->smarty->assign('missing',
      $this->label_sequence_model->has_missing($id));
    $this->smarty->assign('bad_multiple',
      $this->label_sequence_model->has_bad_multiple($id));

    $this->smarty->view('sequence/view');
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
    $seqs = import_fasta_file($this, $file);

    foreach($seqs as &$seq)
    {
      $seq['short_content'] = sequence_short_content($seq['content']);
    }

    $this->smarty->assign('sequences', $seqs);

    $this->smarty->assign('title', 'Batch results');
    $this->smarty->assign('file', $file);
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

    $this->load->library('input');
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

  function delete($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->sequence_model->delete($id);

    redirect('sequence/browse');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('seq');
    $value = $this->input->post('value');

    $size = strlen($value);
    if($size < 3 || $size > 255) {
      $name = $this->sequence_model->get_name($id);
      echo $name;
      return;
    }

    $this->sequence_model->edit_name($id, $value);

    echo $value;
  }

  function edit_content()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('seq');
    $value = $this->input->post('value');

    $value = sequence_join($value);

    $this->sequence_model->edit_content($id, $value);

    echo sequence_short_content($value) . "...";
  }

  function export($id = null)
  {
    if(!$id) {
      $id = $this->get_parameter('id');
    }

    if(!$this->sequence_model->has_sequence($id)) {
      return;
    }

    $this->load->model('label_sequence_model');


    $this->export_sequences(array($id));
  }

  function export_sequences($sequences_id)
  {
    $sequences = array();
    $seq_labels = array();

    foreach($sequences_id as $id) {
      $sequences[] = $this->sequence_model->get($id);
      $seq_labels[] = $this->label_sequence_model->get_sequence($id);
    }

    header('Content-type: text/plain');
    echo export_sequences($sequences, $seq_labels);
  }
}
