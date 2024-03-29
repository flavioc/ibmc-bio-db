<?php

class BioController extends Controller
{
  protected $smarty;

  function BioController()
  {
    parent::__construct();
    
    if(array_key_exists('QUERY_STRING', $_SERVER)) {
      parse_str($_SERVER['QUERY_STRING'], $_GET);
    }

    $this->logged_in = $this->session->userdata('logged_in');
    $this->username = $this->session->userdata('username');
    $this->user_type = $this->session->userdata('user_type');
    $this->user_id = $this->session->userdata('user_id');
    $this->is_admin = $this->user_type == 'admin';

    $CI =& get_instance();
    $CI->logged_in = $this->logged_in;
    $CI->username = $this->username;
    $CI->user_type = $this->user_type;
    $CI->user_id = $this->user_id;
    $CI->is_admin = $this->is_admin;

    $this->smarty = $this->biosmarty;
    $this->smarty->set_controller($this);
    $this->smarty->assign('elapsed_time', $this->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end'));
    $this->smarty->assign('logged_in', $this->logged_in);
    $this->smarty->assign('user_id', $this->user_id);
    $this->smarty->assign('username', $this->username);
    $this->smarty->assign('user_type', $this->user_type);
    $this->smarty->assign('is_admin', $this->is_admin);

    $error_msg = $this->session->flashdata('error_msg');
    if($error_msg) {
      $this->smarty->assign('error_msg', $error_msg);
    }

    $info_msg = $this->session->flashdata('info_msg');
    if($info_msg) {
      $this->smarty->assign('info_msg', $info_msg);
    }

    $this->smarty->load_scripts(JSON_SCRIPT, COOKIE_SCRIPT, TEXTGROW_SCRIPT);
    $this->use_highlight();
    
    $search_term = $this->session->userdata('search_term');
    if($search_term) {
      $this->smarty->assign('search_term_input', htmlspecialchars($search_term));
    }
    
    $this->use_livequery();
    
    if(!$this->logged_in) {
      $this->smarty->load_scripts(VALIDATE_SCRIPT);
    }
    
    // background stuff
    $this->load->model('file_model');
    $has_background = $this->file_model->has_background();
    $this->smarty->assign('has_background', $has_background);
    if($has_background)
      $this->smarty->assign('background_ext', $this->file_model->get_background_extension());
  }

  protected function use_paging_size()
  {
    if($this->logged_in) {
      $this->load->model('configuration_model');
      $this->smarty->assign('paging_size', $this->configuration_model->get_paging_size());
    }
  }

  protected function use_autocomplete()
  {
    $this->smarty->load_scripts(AUTOCOMPLETE_SCRIPT);
    $this->smarty->load_stylesheets(AUTOCOMPLETE_THEME);
  }
  
  protected function use_datepicker()
  {
    $this->smarty->load_scripts(DATEPICKER_SCRIPT);
    $this->smarty->load_stylesheets(DATEPICKER_THEME);
  }

  protected function use_thickbox()
  {
    $this->smarty->load_scripts(THICKBOX_SCRIPT);
    $this->smarty->load_stylesheets(THICKBOX_THEME);
  }

  protected function use_impromptu()
  {
    $this->smarty->load_scripts(IMPROMPTU_SCRIPT, AJAXIMPROMPTU_SCRIPT);
    $this->smarty->load_stylesheets(IMPROMPTU_THEME);
  }

  protected function use_livequery()
  {
    $this->smarty->load_scripts(LIVEQUERY_SCRIPT);
  }

  protected function use_mygrid()
  {
    $this->smarty->load_stylesheets(MYGRID_THEME);
    $this->smarty->load_scripts(JSON_SCRIPT, APPENDDOM_SCRIPT,
      CONFIRM_SCRIPT, JEDITABLE_SCRIPT, MYGRID_SCRIPT);
  }

  protected function use_plusminus()
  {
    $this->smarty->load_scripts(ZOOM_SCRIPT, PLUSMINUS_SCRIPT);
  }
  
  protected function use_highlight()
  {
    $this->smarty->load_scripts('ui/effects.core.js', 'ui/effects.highlight.js');
  }
  
  protected function use_blockui()
  {
    $this->smarty->load_scripts(BLOCKUI_SCRIPT);
  }

  protected function set_form_error($what, $msg)
  {
    $error_str = build_error_name($what);
    $this->session->set_flashdata($error_str, $msg);
  }
  
  public function get_form_error($what)
  {
    $error_str = build_error_name($what);
    return $this->session->flashdata($error_str);
  }

  protected function set_upload_form_error($what)
  {
    $this->session->set_flashdata(build_error_name($what), $this->upload->display_errors('', ''));
  }

  protected function get_post($what)
  {
    return $this->input->xss_clean(trim($this->input->post($what)));
  }

  protected function get_post_filename($what)
  {
    return $_FILES[$what]['name'];
  }

  protected function set_form_value($what)
  {
    $initial = $this->get_post($what);
    if($initial) {
      $this->session->set_flashdata(build_initial_name($what), $initial);
    }
  }

  protected function assign_row_data($what, $with_initial = true)
  {
    $msg = form_error($what);

    if($msg) {
      $this->set_form_error($what, $msg);
    }

    if($with_initial) {
      $this->set_form_value($what);
    }
  }

  protected function set_error_message($msg)
  {
    $this->session->set_flashdata('error_msg', $msg);
  }
  
  protected function set_info_message($msg)
  {
    $this->session->set_flashdata('info_msg', $msg);
  }

  // FIXME: should be protected, but because of BioSmarty it's public
  public function get_parameter($what)
  {
    if(array_key_exists($what, $_GET)) {
      return trim($this->input->xss_clean($_GET[$what]));
    } else {
      return null;
    }
  }

  protected function json_return($val)
  {
    echo json_encode($val);
  }

  protected function return_empty()
  {
    echo "---";
  }

  protected function invalid_permission($msg = null)
  {
    $url = uri_string();

    if(!$msg) {
      $msg = "You must login first";
    }

    $this->set_error_message("$url: $msg");
    redirect("welcome/index?redirect=$url");
  }

  protected function invalid_permission_admin()
  {
    return $this->invalid_permission("Please login as admin");
  }

  protected function invalid_json_permission($val)
  {
    $this->json_return($val);
    return false;
  }

  protected function invalid_permission_empty()
  {
    return $this->invalid_json_permission(array());
  }

  protected function invalid_permission_field()
  {
    return $this->invalid_json_permission("Please login!");
  }

  protected function invalid_permission_false()
  {
    return $this->invalid_json_permission(false);
  }

  protected function invalid_permission_zero()
  {
    return $this->invalid_json_permission(0);
  }

  protected function invalid_permission_thickbox()
  {
    echo "Please login";
    return false;
  }

  protected function invalid_permission_nothing()
  {
    return false;
  }

  protected function get_order($what, $method = 'get')
  {
    $param = "order_$what";
    
    if($method == 'get') {
      return $this->get_parameter($param);
    } else {
      return $this->get_post($param);
    }
  }

  protected function __get_label_types($only_searchable)
  {
    $ret = array('integer', 'float', 'text', 'position', 'ref', 'tax', 'url', 'bool', 'date');

    if(!$only_searchable) {
      array_push($ret, 'obj');
    }

    return build_data_array($ret);
  }

  protected function assign_label_types($only_searchable = false)
  {
    $this->smarty->assign('types', $this->__get_label_types($only_searchable));
  }
  
  protected function __get_xml_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'xml';
    $config['any_file'] = true;
    $config['max_size'] = 2048 * 10; // 10MB

    return $config;
  }
  
  protected function __get_obj_label_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['any_file'] = true;
    $config['max_size'] = 2048 * 10; // 10MB

    return $config;
  }
  
  protected function __read_uploaded_file($name, $config)
  {
    $this->load->library('upload', $config);
    
    if(!$this->upload->do_upload($name)) {
      throw new Exception($this->upload->display_errors('', ''));
    }

    $data = $this->upload->data();

    $filename = $data['orig_name'];
    $bytes = read_file_content($data);
    
    return array('filename' => $filename,
                  'bytes' => $bytes);
  }
  
  protected function __add_label($seq_id, $label_id, $generate, $param = null)
  {
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
    
    if($this->label_sequence_model->label_used_up($seq_id, $label_id)) {
      return "Label is already being used";
    }
    
    $label = $this->label_model->get($label_id);
    if(!$label) {
      return "Could not found label";
    }

    if($generate) {
      return $this->label_sequence_model->add_generated_label($seq_id, $label_id);
    }
    
    switch($label['type']) {
      case 'url':
        return $this->label_sequence_model->add_url_label($seq_id, $label_id, new LabelData($this->get_post('url'), $param));
      case 'text':
        return $this->label_sequence_model->add_text_label($seq_id, $label_id, new LabelData($this->get_post('text'), $param));
      case 'tax':
        $tax = $this->get_post('hidden_tax');

        $this->load->model('taxonomy_model');
        if(!$this->taxonomy_model->has_taxonomy($tax)) {
          $tax_name = $this->get_post('tax');
          return "Taxonomy with id $tax [$tax_name] doesn't exist";
        }

        return $this->label_sequence_model->add_tax_label($seq_id, $label_id, new LabelData($tax, $param));
      case 'ref':
        $ref = $this->get_post('hidden_ref');

        if(!$this->sequence_model->has_sequence($ref)) {
          $ref_name = $this->get_post('ref');
          return "Sequence with id $ref [$ref_name] doesn't exist";
        }

        return $this->label_sequence_model->add_ref_label($seq_id, $label_id, new LabelData($ref, $param));
      case 'position':
        $start = $this->get_post('start');
        $length = $this->get_post('length');
        
        return $this->label_sequence_model->add_position_label($seq_id, $label_id, $start, $length, $param);
      case 'obj':
        try {
          $stored_file = $this->get_post('stored_file');

          if($stored_file) {
            if($param)
              $stored_file = new LabelData($stored_file, $param);
            
            return $this->label_sequence_model->add_obj_label($seq_id, $label_id, $stored_file);
          }
          
          $data = $this->__read_uploaded_file('file', $this->__get_obj_label_config());
          
          $filename = $data['filename'];
          $bytes = $data['bytes'];
          $obj = new FileObject($filename, $bytes);
          
          if($param)
            $obj = new LabelData($obj, $param);
          
          return $this->label_sequence_model->add_obj_label($seq_id, $label_id, $obj);
        } catch(Exception $e) {
          return $e->getMessage();
        }
      case 'integer':
        return $this->label_sequence_model->add_integer_label($seq_id, $label_id, new LabelData($this->get_post('integer'), $param));
      case 'float':
        return $this->label_sequence_model->add_float_label($seq_id, $label_id, new LabelData($this->get_post('float'), $param));
      case 'bool':
        return $this->label_sequence_model->add_bool_label($seq_id, $label_id,
            new LabelData($this->get_post('boolean') ? TRUE : FALSE, $param));
      case 'date':
        return $this->label_sequence_model->add_date_label($seq_id, $label_id,
            new LabelData($this->get_post('date'), $param));
      default:
        return "Label type is invalid";
    }
  }
  
  protected function __edit_label($id, $generate, $param = null)
  {
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
    
    if(!$this->label_sequence_model->label_exists($id)) {
      return "Label/Sequence with id $id doesn't exist";
    }
    
    if($generate) {
      if($this->label_sequence_model->edit_auto_label($id)) {
        return true;
      }

      return "Error generating label $id";
    }
    
    $label = $this->label_sequence_model->get($id);
    
    switch($label['type']) {
      case 'bool':
        return $this->label_sequence_model->edit_bool_label($id, new LabelData($this->get_post('boolean') ? TRUE : FALSE, $param));
      case 'integer':
        return $this->label_sequence_model->edit_integer_label($id, new LabelData($this->get_post('integer'), $param));
      case 'float':
        return $this->label_sequence_model->edit_float_label($id, new LabelData($this->get_post('float'), $param));
      case 'obj':
        try {
          $stored_file = $this->get_post('stored_file');

          if($stored_file) {
            if($param)
              $stored_file = new LabelData($stored_file, $param);
              
            return $this->label_sequence_model->edit_obj_label($id, $stored_file);
          }
          
          $data = $this->__read_uploaded_file('file', $this->__get_obj_label_config());
          
          $filename = $data['filename'];
          $bytes = $data['bytes'];
          $obj = new FileObject($filename, $bytes);
          
          if($param)
            $obj = new LabelData($obj, $param);
          
          return $this->label_sequence_model->edit_obj_label($id, $obj);
        } catch(Exception $e) {
          return $e->getMessage();
        }
      case 'position':
        return $this->label_sequence_model->edit_position_label($id,
            $this->get_post('start'), $this->get_post('length'), $param);
      case 'ref':
        $ref = $this->get_post('hidden_ref');

        if(!$this->sequence_model->has_sequence($ref)) {
          $ref_name = $this->get_post('ref');
          return "Sequence with id $ref [$ref_name] doesn't exist";
        }

        return $this->label_sequence_model->edit_ref_label($id, new LabelData($ref, $param));      
      case 'tax':
        $tax = $this->get_post('hidden_tax');

        $this->load->model('taxonomy_model');
        if(!$this->taxonomy_model->has_taxonomy($tax)) {
          $tax_name = $this->get_post('tax');
          return "Taxonomy with id $tax [$tax_name] doesn't exist";
        }

        return $this->label_sequence_model->edit_tax_label($id, new LabelData($tax, $param));
      case 'text':
        return $this->label_sequence_model->edit_text_label($id, new LabelData($this->get_post('text'), $param));
      case 'url':
        return $this->label_sequence_model->edit_url_label($id, new LabelData($this->get_post('url'), $param));
      case 'date':
        return $this->label_sequence_model->edit_date_label($id, new LabelData($this->get_post('date'), $param));
      default:
        return "Label/Sequence id $id with invalid type";
    }
  }
  
  protected function __get_transform_label($key = 'transform', $mode = 'get')
  {
    if($mode == 'get') {
      $transform = $this->get_parameter($key);
    } else {
      $transform = $this->get_post($key);
    }
    
    if(!$transform || $transform == '0' || !is_numeric($transform)) {
      return null;
    }
    
    $this->load->model('label_model');
    
    $transform = intval($transform);
    $label = $this->label_model->get($transform);
    
    if(!$label || $label['type'] != 'ref') {
      return null;
    }
    
    return $transform;
  }
  
  protected function cookie_exists($name)
  {
    return get_cookie($name) != null;
  }
  
  protected function set_daily_cookie($name, $value)
  {
    $cookie = array(
              'name'   => $name,
              'value'  => $value,
              'expire' => time() + 60 * 60 * 24
    );
    
    set_cookie($cookie);
  }
  
  protected function set_paging_size_cookie($value)
  {
    $this->set_daily_cookie('paging-size', $value);
  }
  
  protected function more_time_limit()
  {
    set_time_limit(60 * 10); // 10 minutes...
  }
  
  protected function __get_search_term($method = 'get', $param = 'search', &$string = null, &$raw = null)
  {
    if($method == 'get') {
      $search = $this->get_parameter($param);
    } else {
      $search = $this->get_post($param);
    }
    $raw = $search;
    $search = addmyslashes($search);
    
    $string = $search;
      
    $search_term = null;
    
    if($search) {
      $search_term = json_decode($search, true);
    }

    return $search_term;
  }
}
