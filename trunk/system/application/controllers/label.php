<?php

class Label extends BioController {

  function Label()
  {
    parent::BioController();
  }

  function browse()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    $this->use_mygrid();
    $this->smarty->assign('title', 'View labels');
    $this->smarty->view('label/list');
  }

  function get_all()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    $labels = $this->label_model->get_all();

    echo json_encode($labels);
  }

  function total_sequences($id)
  {
    if(!$this->logged_in) {
      return;
    }

    // FIXME
    echo json_encode(4);
  }

  function view($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    $label = $this->label_model->get($id);

    $this->smarty->assign('title', 'View label');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->__assign_types();
    $this->smarty->assign('label', $label);
    $this->smarty->view('label/view');
  }

  function __get_types()
  {
    return build_data_array(array('integer', 'text', 'obj', 'position', 'ref', 'tax', 'url'));
  }

  function __assign_types()
  {
    $this->smarty->assign('types', $this->__get_types());
  }

  function add()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->smarty->assign('title', 'Add label');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('type');
    $this->smarty->fetch_form_row('autoadd');
    $this->smarty->fetch_form_row('mustexist');
    $this->smarty->fetch_form_row('auto_on_creation');
    $this->smarty->fetch_form_row('auto_on_modification');
    $this->smarty->fetch_form_row('deletable');
    $this->smarty->fetch_form_row('code');
    $this->smarty->fetch_form_row('comment');

    $this->__assign_types();

    $this->smarty->view('label/add');
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

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    if(!$errors) {
      $this->load->model('label_model');
      $name = $this->get_post('name');

      if($this->label_model->has($name)) {
        $this->set_form_error('name', "Name is already being used.");
        $errors = true;
      }
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('type');
      $this->assign_row_data('autoadd');
      $this->assign_row_data('mustexist');
      $this->assign_row_data('auto_on_creation');
      $this->assign_row_data('auto_on_modification');
      $this->assign_row_data('deletable');
      $this->assign_row_data('code');
      $this->assign_row_data('comment');

      redirect('label/add');
    } else {
      $type = $this->get_post('type');
      $autoadd = $this->get_post('autoadd');
      $mustexist = $this->get_post('mustexist');
      $auto_on_creation = $this->get_post('auto_on_creation');
      $auto_on_modification = $this->get_post('auto_on_modification');
      $deletable = $this->get_post('deletable');
      $code = $this->get_post('code');
      $comment = $this->get_post('comment');

      $autoadd = ($autoadd ? TRUE : FALSE);
      $mustexist = ($mustexist ? TRUE : FALSE);
      $auto_on_creation = ($auto_on_creation ? TRUE : FALSE);
      $auto_on_modification = ($auto_on_modification ? TRUE : FALSE);
      $deletable = ($deletable ? TRUE : FALSE);

      $id = $this->label_model->add($name, $type, $autoadd,
        $mustexist, $auto_on_creation,
        $auto_on_modification, $deletable,
        $code, $comment);

      redirect("label/view/$id");
    }
  }

  function delete($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    if($this->label_model->is_default($id)) {
      return;
    }

    echo json_encode($this->label_model->delete($id));
  }

  function delete_redirect($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    if($this->label_model->is_default($id)) {
      return;
    }

    $this->label_model->delete($id);

    redirect('label/browse');
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $this->load->model('label_model');

    $size = strlen($value);
    if($size < 3 || $size > 255) {
      $name = $this->label_model->get_name($id);
      echo $name;
      return;
    }

    if($this->label_model->has($value)) {
      echo $this->label_model->get_name($id);
      return;
    }

    $this->label_model->edit_name($id, $value);

    echo $value;
  }

  function edit_type()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $this->load->model('label_model');

    $this->label_model->edit_type($id, $value);

    echo $value;
  }

  function edit_autoadd($id, $yes)
  {
    if(!$this->logged_in) {
      return;
    }

    $value = parse_yes($yes);

    $this->load->model('label_model');

    $this->label_model->edit_autoadd($id, $value);
  }

  function edit_mustexist($id, $yes)
  {
    if(!$this->logged_in) {
      return;
    }

    $value = parse_yes($yes);

    $this->load->model('label_model');

    $this->label_model->edit_mustexist($id, $value);
  }

  function edit_auto_on_creation($id, $yes)
  {
    if(!$this->logged_in) {
      return;
    }

    $value = parse_yes($yes);

    $this->load->model('label_model');

    $this->label_model->edit_auto_on_creation($id, $value);
  }

  function edit_auto_on_modification($id, $yes)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    $this->label_model->edit_auto_on_modification($id,
      parse_yes($yes));
  }

  function edit_deletable($id, $yes)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');

    $this->label_model->edit_deletable($id,
      parse_yes($yes));
  }

  function edit_comment()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->get_post('label');
    $value = $this->input->post('value');

    $this->load->model('label_model');

    $this->label_model->edit_comment($id, $value);

    echo $value;
  }

  function edit_code()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id = $this->get_post('label');
    $value = $this->input->post('value');

    $this->load->model('label_model');

    $this->label_model->edit_code($id, $value);

    echo $value;
  }
}
