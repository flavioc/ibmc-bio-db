<?php

class Label_Sequence extends BioController {
  private static $label_inexistant_error = "This label does not exist.";
  private static $label_generate_error = "An error has ocurred while generating the label.";
  private static $label_used_error = "This label is already being used and cannot be reused";
  private static $label_invalid_text_type = "This label has invalid text type";
  private static $label_invalid_bool_type = "This label has invalid integer type";
  private static $label_invalid_url_type = "This label has invalid url type";
  private static $label_invalid_integer_type = "This label has invalid integer type";
  private static $label_invalid_obj_type = "This label has invalid object type";
  private static $label_invalid_position_type = "This label has invalid position type";
  private static $label_invalid_ref_type = "This label has invalid ref type";
  private static $label_invalid_tax_type = "This label has invalid tax type";

  function Label_Sequence()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }

  function get_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $labels = $this->label_sequence_model->get_sequence($id);

    $this->json_return($labels);
  }

  function get_missing_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_missing_obligatory($id);

    $this->json_return($data);
  }

  function get_addable_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_addable_labels($id);

    $this->json_return($data);
  }

  function get_validation_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_validation_labels($id);

    $this->json_return($data);
  }

  function get_bad_multiple_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_bad_multiple($id);

    $this->json_return($data);
  }

  function download_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $label = $this->label_sequence_model->get_id($id);

    $name = $label['text_data'];
    $data = $label['obj_data'];

    header("Content-Disposition: attachment; filename=\"$name\"");

    echo stripslashes($label['obj_data']);
  }

  function edit_subname() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = $this->input->post('value');

    $this->label_sequence_model->edit_subname($id, $value);

    echo $value;
  }

  function delete_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->json_return($this->label_sequence_model->delete($id));
  }

  function edit_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_sequence_model->get($id);
    $this->smarty->assign('label', $label);

    $seq_id = $label['seq_id'];
    $sequence = $this->sequence_model->get($seq_id);
    $this->smarty->assign('sequence', $sequence);

    $editable = $label['editable'];
    $auto = $label['auto_on_creation'];

    if(!$editable && $auto) {
      $this->smarty->view_s('edit_label/auto');
    } else if($editable) {
      $type = $label['type'];

      switch($type) {
        case 'text':
          $this->smarty->view_s('edit_label/text');
          break;
        case 'integer':
          $this->smarty->view_s('edit_label/integer');
          break;
      }
    }
  }

  function add_label($seq_id, $label_id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_thickbox();
    }

    $label = $this->label_model->get($label_id);

    $this->smarty->assign('sequence', $this->sequence_model->get($seq_id));
    $this->smarty->assign('label', $label);

    $editable = $label['editable'];
    $auto = $label['auto_on_creation'];

    if(!$editable && $auto) {
      $this->smarty->view_s('new_label/auto');
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
        case 'position':
          $this->smarty->view_s('new_label/position');
          break;
        case 'ref':
          $this->smarty->view_s('new_label/ref');
          break;
        case 'tax':
          $this->__display_tax_form();
          break;
      }
    } else {
      echo "NOT HANDLED, PLEASE REPORT";
    }
  }

  function __display_tax_form()
  {
    $this->load->model('taxonomy_rank_model');
    $ranks = $this->taxonomy_rank_model->get_ranks();

    $this->load->model('taxonomy_tree_model');
    $trees = $this->taxonomy_tree_model->get_trees();

    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('trees', $trees);

    $this->smarty->view_s('new_label/tax');
  }

  function __get_generate()
  {
    $ret = $this->get_post('generate_check');

    return $ret ? TRUE : FALSE;
  }

  function __add_text_label($seq_id, $label_id, $text, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_text_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_text_type;
    }

    if($this->label_sequence_model->add_text_label($seq_id, $label_id, $text)) {
      return true;
    }

    return self::$label_invalid_text_type;
  }

  function add_text_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $text = $this->get_post('text');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_text_label($seq_id, $label_id, $text, $generate));
  }

  function __edit_text_label($id, $text, $generate)
  {
    if(!$this->label_sequence_model->label_exists($id)) {
      return self::$label_inexistant_error;
    }

    if($generate) {
      if($this->label_sequence_model->edit_generated_text_label($id)) {
        return true;
      }

      return self::$label_generate_error;
    }

    if($this->label_sequence_model->edit_text_label($id, $text)) {
      return true;
    }

    return self::$label_invalid_text_type;
  }

  function edit_text_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');
    $text = $this->get_post('text');
    $generate = $this->__get_generate();

    $this->json_return($this->__edit_text_label($id, $text, $generate));
  }

  function __add_bool_label($seq_id, $label_id, $bool, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_bool_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_bool_type;
    }

    if($this->label_sequence_model->add_bool_label($seq_id, $label_id, $bool)) {
      return true;
    }

    return self::$label_invalid_bool_type;
  }

  function add_bool_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $bool = $this->get_post('boolean');
    $generate = $this->__get_generate();

    $bool = ($bool ? TRUE : FALSE);

    $this->json_return($this->__add_bool_label($seq_id, $label_id, $bool, $generate));
  }

  function __add_integer_label($seq_id, $label_id, $int, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_integer_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_integer_type;
    }

    if($this->label_sequence_model->add_integer_label($seq_id, $label_id, $int)) {
      return true;
    } else {
      return self::$label_invalid_integer_type;
    }
  }

  function add_integer_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $int = intval($this->get_post('integer'));
    $generate = $this->__get_generate();

    $this->json_return($this->__add_integer_label($seq_id, $label_id, $int, $generate));
  }

  function __edit_integer_label($id, $integer, $generate)
  {
    if(!$this->label_sequence_model->label_exists($id)) {
      return self::$label_inexistant_error;
    }

    if($generate) {
      if($this->label_sequence_model->edit_generated_integer_label($id)) {
        return true;
      }

      return self::$label_generate_error;
    }

    if($this->label_sequence_model->edit_integer_label($id, $integer)) {
      return true;
    }

    return self::$label_invalid_integer_type;
  }

  function edit_integer_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_zero();
    }

    $id = $this->get_post('id');
    $integer = $this->get_post('integer');
    $generate = $this->__get_generate();

    $this->json_return($this->__edit_integer_label($id, $integer, $generate));
  }

  function __add_obj_label($seq_id, $label_id, $generate)
  {
    if($generate) {
      if($this->label_sequence_model->add_generated_obj_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_obj_type;
    }

    $this->load->library('upload', $this->_get_upload_config());
    $upload_ret = $this->upload->do_upload('file');

    if(!$upload_ret) {
      return $this->upload->display_errors('', '');;
    }

    $data = $this->upload->data();

    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    $this->load->helper('image_utils');
    $filename = $data['orig_name'];
    $bytes = read_file_content($data);

    if($this->label_sequence_model->add_obj_label($seq_id,
      $label_id, $filename, $bytes))
    {
      return true;
    } else {
      return self::$label_invalid_obj_type;
    }
  }

  function add_obj_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_obj_label($seq_id, $label_id, $generate));
  }

  function __add_position_label($seq_id, $label_id, $start, $length, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_position_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_position_type;
    }

    if($this->label_sequence_model->add_position_label($seq_id, $label_id,
      intval($start), intval($length)))
    {
      return true;
    } else {
      return self::$label_invalid_position_type;
    }
  }

  function add_position_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $start = $this->get_post('start');
    $length = $this->get_post('length');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_position_label($seq_id, $label_id, $start, $length, $generate));
  }

  function add_tax_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $tax = $this->get_post('hidden_tax');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_tax_label($seq_id, $label_id, $tax, $generate));
  }

  function __add_tax_label($seq_id, $label_id, $tax, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_tax_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_tax_type;
    }

    $tax = intval($tax);

    $this->load->model('taxonomy_model');
    if(!$this->taxonomy_model->has_taxonomy($tax)) {
      return "Taxonomy doesn't exist.";
    }

    if($this->label_sequence_model->add_tax_label($seq_id, $label_id, $tax)) {
      return true;
    }

    return self::$label_invalid_tax_label;
  }

  function __add_ref_label($seq_id, $label_id, $ref, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_ref_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_ref_type;
    }

    $ref = intval($ref);

    if(!$this->sequence_model->has_sequence($ref)) {
      return "Sequence doesn't exist.";
    }

    if($this->label_sequence_model->add_ref_label($seq_id, $label_id, $ref)) {
      return true;
    }

    return self::$label_invalid_ref_label;
  }

  function add_ref_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $ref = $this->get_post('hidden_ref');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_ref_label($seq_id, $label_id, $ref, $generate));
  }

  function __add_url_label($seq_id, $label_id, $url, $generate)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    }

    if($generate) {
      if($this->label_sequence_model->add_generated_url_label($seq_id, $label_id)) {
        return true;
      }

      return self::$label_invalid_url_type;
    }

    if($this->label_sequence_model->add_url_label($seq_id, $label_id, $url)) {
      return true;
    } else {
      return self::$label_invalid_url_type;
    }
  }

  function add_url_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');
    $url = $this->get_post('url');
    $generate = $this->__get_generate();

    $this->json_return($this->__add_url_label($seq_id, $label_id, $url, $generate));
  }

  function __add_auto_label($seq_id, $label_id)
  {
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return self::$label_used_error;
    } else {
      $this->label_sequence_model->add_auto_label_id($seq_id, $label_id);
      return true;
    }
  }

  function add_auto_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $seq_id = $this->get_post('seq_id');
    $label_id = $this->get_post('label_id');

    $this->json_return($this->__add_auto_label($seq_id, $label_id));
  }

  function __edit_auto_label($id)
  {
    if($this->label_sequence_model->label_exists($id)) {
      $ret = $this->label_sequence_model->edit_auto_label($id);

      if($ret) {
        return true;
      } else {
        return self::$label_generate_error;
      }
    } else {
      return self::$label_inexistant_error;
    }
  }

  function edit_auto_label()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');

    $this->json_return($this->__edit_auto_label($id));
  }

  function _get_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'doc|fasta|pdf|xls|docx|xlsx|png|bmp|gif|jpg|hs';

    return $config;
  }
}
