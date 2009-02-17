<?php

class Sequence extends BioController
{
  function Sequence() {
    parent::BioController();
    $this->load->model('sequence_model');
  }

  function __get_types()
  {
    return build_data_array(array('dna', 'protein'));
  }

  function __assign_types()
  {
    $this->smarty->assign('types', $this->__get_types());
  }

  function browse()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->smarty->assign('title', 'Browse sequences');

    $this->use_mygrid();
    $this->use_paging_size();

    $this->smarty->view('sequence/list');
  }

  function get_all()
  {
    if(!$this->logged_in) {
      return;
    }

    $start = $this->get_parameter('start');
    $size = $this->get_parameter('size');

    echo json_encode($this->sequence_model->get_all($start, $size));
  }

  function get_total()
  {
    if(!$this->logged_in) {
      return;
    }

    echo json_encode($this->sequence_model->get_total());
  }

  function view($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->smarty->assign('title', 'View sequence');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);

    $this->__assign_types();

    $this->smarty->assign('sequence', $this->sequence_model->get($id));

    $this->smarty->view('sequence/view');
  }

  function add()
  {
    $this->smarty->assign('title', 'Add sequence');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    $this->__assign_types();

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('type');
    $this->smarty->fetch_form_row('content');
    $this->smarty->fetch_form_row('accession');

    $this->smarty->view('sequence/add');
  }

  function _add_labels($id)
  {
    // add auto labels
    $this->load->model('label_model');
    $labels = $this->label_model->get_to_add();
    print_r($labels);
  }

  function do_add()
  {
    if(!$this->logged_in) {
      return;
    }

    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[255]');
    $this->form_validation->set_rules('content', 'Content', 'trim|required|max_length[65535]');
    $this->form_validation->set_rules('accession', 'Accession Number', 'trim|max_length[255]');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('content');
      $this->assign_row_data('type');
      $this->assign_row_data('accession');

      redirect('sequence/add');
    } else {
      $name = $this->get_post('name');
      $accession = $this->get_post('accession');
      $type = $this->get_post('type');
      $content = $this->get_post('content');

      $id = $this->sequence_model->add($name, $accession, $type, $content);

      $this->_add_labels($id);

      redirect("sequence/view/$id");
    }
  }

  function download($id)
  {
    if(!$this->logged_in) {
      return;
    }

    echo $this->sequence_model->get_content($id);
  }

  function delete($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->sequence_model->delete($id);

    redirect('sequence/browse');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return;
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

  function edit_accession()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('seq');
    $value = $this->input->post('value');

    $this->sequence_model->edit_accession($id, $value);

    echo $value;
  }
}
