<?php

class Label extends BioController {

  function Label()
  {
    parent::BioController();
    $this->load->model('label_model');
  }

  function browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    $this->smarty->assign('title', 'View labels');
    $this->smarty->view('label/list');
  }

  function get_all()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $name = $this->get_parameter('name');
    $start = intval($this->get_parameter('start'));
    $size = intval($this->get_parameter('size'));

    $labels = $this->label_model->get_all($name, $start, $size);

    $this->json_return($labels);
  }

  function count_total()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $name = $this->get_parameter('name');

    $total = $this->label_model->get_total($name);

    $this->json_return($total);
  }

  function total_sequences($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->load->model('label_sequence_model');

    $this->json_return($this->label_sequence_model->total_label($id));
  }

  function view($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    if(!$this->label_model->has_label($id)) {
      $this->smarty->assign('title', 'Label not found');
      $this->smarty->assign('id', $id);
      $this->smarty->view('label/not_found');
      return;
    }

    $label = $this->label_model->get($id);

    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->smarty->assign('title', 'Label "' . $label['name'] . '"');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->__assign_types();
    $this->smarty->assign('label', $label);
    $this->use_impromptu();
    $this->smarty->view('label/view');
  }

  function __get_types()
  {
    return build_data_array(array('integer', 'text', 'obj', 'position', 'ref', 'tax', 'url', 'bool'));
  }

  function __assign_types()
  {
    $this->smarty->assign('types', $this->__get_types());
  }

  function add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Add label');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'validate_label.js');

    $this->smarty->fetch_form_row('name');
    $this->smarty->fetch_form_row('type');
    $this->smarty->fetch_form_row('mustexist');
    $this->smarty->fetch_form_row('auto_on_creation');
    $this->smarty->fetch_form_row('auto_on_modification');
    $this->smarty->fetch_form_row('deletable');
    $this->smarty->fetch_form_row('editable');
    $this->smarty->fetch_form_row('multiple');
    $this->smarty->fetch_form_row('default');
    $this->smarty->fetch_form_row('public');
    $this->smarty->fetch_form_row('code');
    $this->smarty->fetch_form_row('valid_code');
    $this->smarty->fetch_form_row('comment');

    $this->__assign_types();

    $this->smarty->view('label/add');
  }

  function __form_validation($max_names)
  {
    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[255]');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    if(!$errors) {
      $name = $this->get_post('name');

      if($this->label_model->count_names($name) > $max_names) {
        $this->set_form_error('name', "Name is already being used.");
        $errors = true;
      }

      $auto_on_creation = $this->get_post('auto_on_creation');
      $auto_on_creation = ($auto_on_creation ? TRUE : FALSE);
      $auto_on_modification = $this->get_post('auto_on_modification');
      $auto_on_modification = ($auto_on_modification ? TRUE : FALSE);
      $code = $this->get_post('code');
      $valid_code = $this->get_post('valid_code');

      if($auto_on_creation || $auto_on_modification) {
        if(!$code || strlen($code) == 0) {
          $this->set_form_error('code', 'Code must be defined');
          $errors = true;
        }
      }
    }

    if($errors) {
      $this->assign_row_data('name');
      $this->assign_row_data('type');
      $this->assign_row_data('mustexist');
      $this->assign_row_data('auto_on_creation');
      $this->assign_row_data('auto_on_modification');
      $this->assign_row_data('deletable');
      $this->assign_row_data('editable');
      $this->assign_row_data('multiple');
      $this->assign_row_data('default');
      $this->assign_row_data('public');
      $this->assign_row_data('code');
      $this->assign_row_data('valid_code');
      $this->assign_row_data('comment');

      return null;
    } else {
      $type = $this->get_post('type');
      $mustexist = $this->get_post('mustexist');
      $deletable = $this->get_post('deletable');
      $editable = $this->get_post('editable');
      $multiple = $this->get_post('multiple');
      $default = $this->get_post('default');
      $public = $this->get_post('public');
      $comment = $this->get_post('comment');

      $mustexist = ($mustexist ? TRUE : FALSE);
      $deletable = ($deletable ? TRUE : FALSE);
      $editable = ($editable ? TRUE : FALSE);
      $multiple = ($multiple ? TRUE : FALSE);
      $default = ($default ? TRUE : FALSE);
      $public = ($public ? TRUE : FALSE);

      return array('name' => $name,
                   'type' => $type,
                   'must_exist' => $mustexist,
                   'auto_on_creation' => $auto_on_creation,
                   'auto_on_modification' => $auto_on_modification,
                   'deletable' => $deletable,
                   'editable' => $editable,
                   'multiple' => $multiple,
                   'default' => $default,
                   'public' => $public,
                   'code' => $code,
                   'valid_code' => $valid_code,
                   'comment' => $comment);
    }
  }

  function do_add()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $result = $this->__form_validation(0);

    if(is_array($result)) {
      $id = $this->label_model->add($result['name'], $result['type'],
        $result['must_exist'], $result['auto_on_creation'],
        $result['auto_on_modification'], $result['deletable'],
        $result['editable'], $result['multiple'],
        $result['default'],
        $result['public'],
        $result['code'],
        $result['valid_code'],
        $result['comment']);

      redirect("label/view/$id");
    } else {
      redirect('label/add');
    }
  }

  function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');
    $this->label_model->delete_label($id);

    redirect('label/browse');
  }

  function delete_dialog($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_nothing();
    }

    $label = $this->label_model->get($id);
    $this->smarty->assign('label', $label);
    $this->load->model('label_sequence_model');
    $num_seq = $this->label_sequence_model->count_sequences_for_label($id);
    $this->smarty->assign('num_seq', $num_seq);

    $this->smarty->view_s('label/delete');
  }

  function edit_name() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $result = $this->label_model->edit_name($id, $value);

    if($result) {
      // Update OK
      echo $value;
    } else {
      // Name already used.
      echo $this->label_model->get_name($id);
    }
  }

  function edit_type() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $result = $this->label_model->edit_type($id, $value);
    echo $value;
  }

  function edit_code() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $result = $this->label_model->edit_code($id, $value);
    echo $value;
  }

  function edit_validcode() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $result = $this->label_model->edit_validcode($id, $value);
    echo $value;
  }

  function edit_comment() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');

    $result = $this->label_model->edit_comment($id, $value);
    echo $value;
  }

  function edit_bool($what) {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id = $this->input->post('label');
    $value = $this->input->post('value');
    $value = ($value == '1' ? TRUE : FALSE);

    $result = $this->label_model->edit_bool($id, $what, $value);

    echo $value ? "Yes" : "No";
  }
}
