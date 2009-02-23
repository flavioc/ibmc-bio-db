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
    $this->smarty->load_scripts(JEDITABLE_SCRIPT, IMPROMPTU_SCRIPT);
    $this->use_mygrid();

    $this->__assign_types();

    $this->smarty->assign('sequence', $this->sequence_model->get($id));

    $this->smarty->view('sequence/view');
  }

  function get_labels($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_sequence_model');
    $labels = $this->label_sequence_model->get_sequence($id);

    echo json_encode($labels);
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
    $this->load->model('label_sequence_model');
    $this->label_sequence_model->add_initial_labels($id);
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

  function edit_content()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('seq');
    $value = $this->input->post('value');

    $this->sequence_model->edit_content($id, $value);

    echo $value;
  }

  function regenerate($seq)
  {
    $this->load->model('label_sequence_model');

    $this->sequence_model->edit_content($seq, 'ABDAD');
//    $this->label_sequence_model->regenerate_labels($seq);
  }

  function edit_subname() {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = $this->input->post('value');

    $this->load->model('label_sequence_model');

    $this->label_sequence_model->edit_subname($id, $value);

    echo $value;
  }

  function delete_label($id) {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_sequence_model');

    echo json_encode($this->label_sequence_model->delete($id));
  }

  function download_label($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_sequence_model');

    $label = $this->label_sequence_model->get_id($id);

    $name = $label['text_data'];
    $data = $label['obj_data'];

    header("Content-Disposition: attachment; filename=\"$name\"");

    echo stripslashes($label['obj_data']);
  }

  function get_missing_labels($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_sequence_model');
    $data = $this->label_sequence_model->get_missing_obligatory($id);

    echo json_encode($data);
  }

  function get_addable_labels($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_sequence_model');

    $data = $this->label_sequence_model->get_addable_labels($id);

    echo json_encode($data);
  }
}
