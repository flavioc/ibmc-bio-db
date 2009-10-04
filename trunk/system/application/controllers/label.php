<?php

class Label extends BioController
{
  function Label()
  {
    parent::BioController();
    $this->load->model('label_model');
  }

  public function browse()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->use_mygrid();
    $this->assign_label_types();
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'label_functions.js');
    $this->use_plusminus();

    $this->load->model('user_model');

    $this->smarty->assign('users', $this->user_model->get_users_all());

    $this->smarty->assign('title', 'View labels');
    $this->smarty->view('label/list');
  }

  public function get_label_by_name($name)
  {
    $label = $this->label_model->get_by_name($name);

    $this->json_return($label);
  }

  public function autocomplete_labels()
  {
    $name = $this->get_parameter('q');
    $type = $this->get_parameter('type');

    switch($type) {
      case 'searchable':
        $labels = $this->label_model->get_all(null, null,
          array('name' => $name,
                'only_public' => !$this->logged_in),
          array('name' => 'asc'));
        break;
      case 'addable':
        $labels = $this->label_model->get_all(null, null,
          array('name' => $name,
                'only_addable' => true),
          array('name' => 'asc'));
        break;
      case 'deletable':
        $labels = $this->label_model->get_all(null, null,
          array('name' => $name,
                'only_deletable' => true),
          array('name' => 'asc'));
        break;
      default:
        $labels = array();
    }
    
    output_autocomplete_data($labels, 'name');
  }

  public function get_all()
  {
    $start = intval($this->get_parameter('start'));
    $size = intval($this->get_parameter('size'));

    $name_filter = $this->get_parameter('name');
    $type_filter = $this->get_parameter('type');
    $user_filter = $this->get_parameter('user');

    $ordering_name = $this->get_order('name');
    $ordering_type = $this->get_order('type');

    $labels = $this->label_model->get_all($start, $size,
      array('name' => $name_filter,
            'type' => $type_filter,
            'user' => $user_filter,
            'only_public' => !$this->logged_in),
      array('name' => $ordering_name,
            'type' => $ordering_type),
      true); # get sequence totals

    $this->json_return($labels);
  }

  public function count_total()
  {
    $name_filter = $this->get_parameter('name');
    $type_filter = $this->get_parameter('type');
    $user_filter = $this->get_parameter('user');

    $total = $this->label_model->get_total(
      array('name' => $name_filter,
            'type' => $type_filter,
            'user' => $user_filter,
            'only_public' => !$this->logged_in));

    $this->json_return($total);
  }

  public function total_sequences($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->load->model('label_sequence_model');

    $this->json_return($this->label_sequence_model->total_label($id));
  }

  public function view($id)
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

    $label['comment'] = newline_tab_html($label['comment']);
    $label['code'] = newline_tab_html($label['code']);
    $label['valid_code'] = newline_tab_html($label['valid_code']);
    
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->smarty->assign('title', 'Label "' . $label['name'] . '"');
    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->assign_label_types();
    $this->smarty->assign('label', $label);
    $this->use_impromptu();
    $this->smarty->load_stylesheets(MYGRID_THEME);
    $this->smarty->view('label/view');
  }

  public function add()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }

    $this->smarty->assign('title', 'Add label');
    $this->smarty->load_scripts(VALIDATE_SCRIPT, 'validate_label.js');
    $this->smarty->load_stylesheets(MYGRID_THEME);

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
    $this->smarty->fetch_form_row('action_modification');
    $this->smarty->fetch_form_row('comment');

    $this->assign_label_types();

    $this->smarty->view('label/add');
  }

  private function __form_validation($max_names)
  {
    $errors = false;

    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[255]');
    $this->form_validation->set_rules('comment', 'Comment', 'trim|max_length[1024]');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    if(!$errors) {
      $name = change_spaces($this->get_post('name'));

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
      $this->assign_row_data('action_modification');
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
      $action_modification = $this->get_post('action_modification');

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
                   'action_modification' => $action_modification,
                   'comment' => $comment);
    }
  }

  public function do_add()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
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
        $result['action_modification'],
        $result['valid_code'],
        $result['comment']);

      redirect("label/view/$id");
    } else {
      redirect('label/add');
    }
  }

  public function delete_redirect()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }

    $id = $this->get_post('id');
    $this->label_model->delete_label($id);

    redirect('label/browse');
  }

  public function delete_dialog($id)
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_nothing();
    }

    $label = $this->label_model->get($id);
    $this->smarty->assign('label', $label);
    $this->load->model('label_sequence_model');
    $num_seq = $this->label_sequence_model->count_sequences_for_label($id);
    $this->smarty->assign('num_seq', $num_seq);

    $this->smarty->view_s('label/delete');
  }

  public function edit_name()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');

    $result = $this->label_model->edit_name($id, $value);

    echo $this->label_model->get_name($id);
  }

  public function edit_type()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');

    $result = $this->label_model->edit_type($id, $value);
    
    echo $this->label_model->get_type($id);
  }

  public function edit_code()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');

    $result = $this->label_model->edit_code($id, $value);
    
    if($value) {
      echo newline_tab_html($value);
    } else {
      $this->return_empty();
    }
  }
  
  public function edit_actionmodification()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }
    
    $id = $this->get_post('label');
    $value = $this->get_post('value');
    
    $result = $this->label_model->edit_actionmodification($id, $value);
    
    if($value) {
      echo newline_tab_html($value);
    } else {
      $this->return_empty();
    }
  }

  public function edit_validcode()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');

    $result = $this->label_model->edit_validcode($id, $value);
    
    if($value) {
      echo newline_tab_html($value);
    } else {
      $this->return_empty();
    }
  }

  public function edit_comment()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');

    $result = $this->label_model->edit_comment($id, $value);
    
    if($result) {
      if($value) {
        echo newline_tab_html($value);
      } else {
        $this->return_empty();
      }
    } else {
      echo newline_tab_html($this->label_model->get_comment($id));
    }
  }
  
  public function get_comment($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }
    
    echo $this->label_model->get_comment($id);
  }
  
  public function get_code($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }
    
    echo $this->label_model->get_code($id);
  }
  
  public function get_validcode($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }
    
    echo $this->label_model->get_validcode($id);
  }
  
  public function get_actionmodification($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }
    
    return $this->label_model->get_actionmodification($id);
  }

  public function edit_bool($what)
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('label');
    $value = $this->get_post('value');
    $value = ($value == '1' ? TRUE : FALSE);

    $result = $this->label_model->edit_bool($id, $what, $value);

    echo $value ? "Yes" : "No";
  }
  
  public function export()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $name_filter = $this->get_post('export_name');
    $type_filter = $this->get_post('export_type');
    $user_filter = $this->get_post('export_user');
    
    $this->__do_export($this->label_model->get_all(null, null,
      array('name' => $name_filter,
            'type' => $type_filter,
            'user' => $user_filter)));
  }
  
  public function export_id()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $id = $this->get_post('id');
    $labels = array($this->label_model->get($id));
    
    $this->__do_export($labels);
  }
  
  private function __do_export($labels)
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="labels.xml"');

    $this->load->library('LabelExporter');
    
    echo $this->labelexporter->export_group($labels);
  }
  
  public function import()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->fetch_form_row('file');
    $this->smarty->assign('title', 'Import labels from file');
    $this->smarty->view('label/import');
  }
  
  public function do_import()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->load->library('upload', $this->__get_xml_upload_config());

    $upload_ret = $this->upload->do_upload('file');

    if($upload_ret) {
      $data = $this->upload->data();
      
      $file = $data['full_path'];
      
      $this->load->library('LabelImporter');
      
      $ret = $this->labelimporter->import_xml($file);
      unlink($file);
      
      if(!$ret) {
        $this->set_form_error('file', 'Error reading the XML file');
        redirect('label/import');
      } else {
        $this->smarty->assign('labels', $ret);
        $this->smarty->assign('title', 'Import labels');
        $this->use_mygrid();
        $this->smarty->view('label/do_import');
      }
    } else {
      $this->set_upload_form_error('file');
      redirect('label/import');
    }
  }
}