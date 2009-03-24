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

    $label = $this->label_model->get($id);

    $this->smarty->assign('title', 'View label');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->__assign_types();
    $this->smarty->assign('label', $label);
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

  function edit()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_parameter('id');
    $this->smarty->assign('title', 'Edit label');
    $this->__assign_types();
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'validate_label.js');

    $label = $this->label_model->get($id);

    $this->smarty->fetch_form_row('name', $label['name']);
    $this->smarty->fetch_form_row('type', $label['type']);
    $this->smarty->fetch_form_row('autoadd', $label['autoadd']);
    $this->smarty->fetch_form_row('must_exist', $label['must_exist']);
    $this->smarty->fetch_form_row('auto_on_creation', $label['auto_on_creation']);
    $this->smarty->fetch_form_row('auto_on_modification', $label['auto_on_modification']);
    $this->smarty->fetch_form_row('deletable', $label['deletable']);
    $this->smarty->fetch_form_row('editable', $label['editable']);
    $this->smarty->fetch_form_row('multiple', $label['multiple']);
    $this->smarty->fetch_form_row('default', $label['default']);
    $this->smarty->fetch_form_row('public', $label['public']);
    $this->smarty->fetch_form_row('code', $label['code']);
    $this->smarty->fetch_form_row('valid_code', $label['valid_code']);
    $this->smarty->fetch_form_row('comment', $label['comment']);

    $this->smarty->assign('label', $label);

    $this->smarty->view('label/edit');
  }

  function do_edit($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $result = $this->__form_validation(1);

    if(is_array($result)) {
      $this->label_model->edit($id, $result['name'], $result['type'],
        $result['autoadd'], $result['must_exist'], $result['auto_on_creation'],
        $result['auto_on_modification'], $result['deletable'],
        $result['editable'], $result['multiple'],
        $result['default'],
        $result['public'],
        $result['code'],
        $result['valid_code'],
        $result['comment']);

      redirect("label/view/$id");
    } else {
      redirect("label/edit/$id");
    }
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
    $this->smarty->fetch_form_row('autoadd');
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

      $autoadd = $this->get_post('autoadd');
      $autoadd = ($autoadd ? TRUE : FALSE);
      $auto_on_creation = $this->get_post('auto_on_creation');
      $auto_on_creation = ($auto_on_creation ? TRUE : FALSE);
      $auto_on_modification = $this->get_post('auto_on_modification');
      $auto_on_modification = ($auto_on_modification ? TRUE : FALSE);
      $code = $this->get_post('code');
      $valid_code = $this->get_post('valid_code');

      if($auto_on_creation) {
        if(!$autoadd) {
          $this->set_form_error('autoadd', 'This must be set to true');
          $errors = true;
        }
      }

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
      $this->assign_row_data('autoadd');
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
                   'autoadd' => $autoadd,
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
        $result['autoadd'], $result['must_exist'], $result['auto_on_creation'],
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

  function delete($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    if($this->label_model->is_default($id)) {
      return;
    }

    $this->json_return($this->label_model->delete($id));
  }

  function delete_redirect($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    if($this->label_model->is_default($id)) {
      return;
    }

    $this->label_model->delete($id);

    redirect('label/browse');
  }
}
