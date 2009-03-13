<?php

class Sequence extends BioController
{
  private static $label_used_error = "This label is already being used and cannot be reused";
  private static $label_invalid_text_type = "This label has invalid text type";

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
    $this->smarty->load_scripts(JSON_SCRIPT,
      FORM_SCRIPT, 'sequence_functions.js');
    $this->use_thickbox();
    $this->use_mygrid();
    $this->use_plusminus();

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

  function add_label($seq_id, $label_id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('label_model');
    $label = $this->label_model->get($label_id);

    $this->smarty->assign('sequence', $this->sequence_model->get($seq_id));
    $this->smarty->assign('label', $label);

    $editable = $label['editable'];
    $auto = $label['auto_on_creation'];

    if(!$editable && $auto) {
      $this->smarty->view_s('new_label/auto');
    } else if($editable && $auto) {
      echo "NOT AUTO";
    } else if($editable) {
      $type = $label['type'];

      switch($type) {
        case 'text':
          $this->smarty->view_s('new_label/text');
          break;
        case 'integer':
          $this->smarty->view_s('new_label/integer');
          break;
        case 'url':
          $this->smarty->view_s('new_label/url');
          break;
        case 'obj':
          $this->smarty->view_s('new_label/obj');
          break;
        case 'bool':
          $this->smarty->view_s('new_label/bool');
          break;
      }
    } else {
      echo "NOT HANDLED";
    }
  }

  function __get_generate()
  {
    $ret = $this->get_post('generate_check');

    return $ret ? TRUE : FALSE;
  }

  function add_text_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $text = $this->get_post('text');
    $generate = $this->__get_generate();

    $ret = null;
    
    $this->load->model('label_sequence_model');

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      $ret = self::$label_used_error;
    } else {
      if($generate) {
        if($this->label_sequence_model->add_generated_text_label($seq_id, $label_id)) {
          $ret = true;
        } else {
          $ret = self::$label_invalid_text_type;
        }
      } else {
        if($this->label_sequence_model->add_text_label($seq_id, $label_id, $text)) {
          $ret = true;
        } else {
          $ret = self::$label_invalid_text_type;
        }
      }
    }

    echo json_encode($ret);
  }

  function add_bool_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $bool = $this->get_post('boolean');

    $bool = ($bool ? TRUE : FALSE);

    $ret = null;

    $this->load->model('label_sequence_model');

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      $ret = self::$label_used_error;
    } else {
      if($this->label_sequence_model->add_bool_label($seq_id, $label_id, $bool)) {
        $ret = true;
      } else {
        $ret = "This label has invalid integer type";
      }
    }

    echo json_encode($ret);
  }

  function add_integer_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $int = intval($this->get_post('integer'));

    $ret = null;

    $this->load->model('label_sequence_model');

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      $ret = self::$label_used_error;
    } else {
      if($this->label_sequence_model->add_integer_label($seq_id, $label_id, $int)) {
        $ret = true;
      } else {
        $ret = "This label has invalid integer type";
      }
    }

    echo json_encode($ret);
  }

  function add_obj_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');

    $this->load->library('upload', $this->_get_upload_config());
    $upload_ret = $this->upload->do_upload('file');
    $ret = null;

    if($upload_ret) {
      $data = $this->upload->data();
      $this->load->model('label_sequence_model');

      if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
        $ret = self::$label_used_error;
      } else {

        $this->load->helper('image_utils');
        $filename = $data['orig_name'];
        $bytes = read_file_content($data);

        if($this->label_sequence_model->add_obj_label($seq_id,
          $label_id, $filename, $bytes))
        {
          $ret = true;
        } else {
          $ret = "This label has invalid object type";
        }
      }
    } else {
      $ret = $this->upload->display_errors('', '');;
    }

    echo json_encode($ret);
  }

  function add_url_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $url = $this->get_post('url');

    $ret = null;

    $this->load->model('label_sequence_model');

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      $ret = self::$label_used_error;
    } else {
      if($this->label_sequence_model->add_url_label($seq_id, $label_id, $url)) {
        $ret = true;
      } else {
        $ret = "This label has invalid url type";
      }
    }

    echo json_encode($ret);
  }

  function add_auto_label()
  {
    if(!$this->logged_in) {
      return;
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');

    $this->load->model('label_sequence_model');

    $ret = null;

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      $ret = self::$label_used_error;
    } else {
      $this->label_sequence_model->add_auto_label_id($seq_id, $label_id);
      $ret = true;
    }

    echo json_encode($ret);
  }

  function _get_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'doc|fasta|pdf|xls|docx|xlsx|png|bmp|gif|jpg';

    return $config;
  }
}
