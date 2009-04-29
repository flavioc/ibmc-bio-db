<?php

class BioController extends Controller
{
  var $smarty;

  function BioController()
  {
    parent::__construct();
    parse_str($_SERVER['QUERY_STRING'], $_GET);

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

    $this->smarty->load_scripts(JSON_SCRIPT, SESSION_SCRIPT, COOKIE_SCRIPT);

    $this->load->model('configuration_model');
    setcookie('paging-size', $this->configuration_model->get_paging_size());

    setcookie('logged-in', $this->logged_in);
    setcookie('username', $this->username);
  }

  function use_paging_size()
  {
    if($this->logged_in) {
      $this->load->model('configuration_model');
      $this->smarty->assign('paging_size', $this->configuration_model->get_paging_size());
    }
  }

  function use_autocomplete()
  {
    $this->smarty->load_scripts(AUTOCOMPLETE_SCRIPT);
    $this->smarty->load_stylesheets(AUTOCOMPLETE_THEME);
  }

  function use_thickbox()
  {
    $this->smarty->load_scripts(THICKBOX_SCRIPT);
    $this->smarty->load_stylesheets(THICKBOX_THEME);
  }

  function use_impromptu()
  {
    $this->smarty->load_scripts(IMPROMPTU_SCRIPT, AJAXIMPROMPTU_SCRIPT);
    $this->smarty->load_stylesheets(IMPROMPTU_THEME);
  }

  function use_mygrid()
  {
    $this->smarty->load_scripts(JSON_SCRIPT, APPENDDOM_SCRIPT,
      CONFIRM_SCRIPT, JEDITABLE_SCRIPT, MYGRID_SCRIPT);
  }

  function use_plusminus()
  {
    $this->smarty->load_scripts(ZOOM_SCRIPT, PLUSMINUS_SCRIPT);
  }

  function set_form_error($what, $msg)
  {
    $error_str = build_error_name($what);
    $this->session->set_flashdata($error_str, $msg);
  }

  function set_upload_form_error($what)
  {
    $this->session->set_flashdata(build_error_name($what), $this->upload->display_errors('', ''));
  }

  function get_post($what)
  {
    return trim($this->input->post($what));
  }

  function get_post_filename($what)
  {
    return $_FILES[$what]['name'];
  }

  function set_form_value($what)
  {
    $initial = $this->get_post($what);
    if($initial) {
      $this->session->set_flashdata(build_initial_name($what), $initial);
    }
  }

  function assign_row_data($what, $with_initial = true)
  {
    $msg = form_error($what);

    if($msg) {
      $this->set_form_error($what, $msg);
    }

    if($with_initial) {
      $this->set_form_value($what);
    }
  }

  function set_error_message($msg)
  {
    $this->session->set_flashdata('error_msg', $msg);
  }

  function get_parameter($what)
  {
    if(array_key_exists($what, $_GET)) {
      return $_GET[$what];
    } else {
      return null;
    }
  }

  function json_return($val)
  {
    echo json_encode($val);
  }

  function return_empty()
  {
    echo "---";
  }

  function invalid_permission($msg = null)
  {
    $url = uri_string();

    if(!$msg) {
      $msg = "You must login first";
    }

    $this->set_error_message("$url: $msg");
    redirect("welcome/index?redirect=$url");
  }

  function invalid_permission_admin()
  {
    return $this->invalid_permission("Please login as admin");
  }

  function invalid_json_permission($val)
  {
    $this->json_return($val);
    return false;
  }

  function invalid_permission_empty()
  {
    return $this->invalid_json_permission(array());
  }

  function invalid_permission_field()
  {
    return $this->invalid_json_permission("Please login!");
  }

  function invalid_permission_false()
  {
    return $this->invalid_json_permission(false);
  }

  function invalid_permission_zero()
  {
    return $this->invalid_json_permission(0);
  }

  function invalid_permission_thickbox()
  {
    echo "Please login";
    return false;
  }

  function invalid_permission_nothing()
  {
    return false;
  }

  function get_order($what)
  {
    $param = "order_$what";
    $get = $this->get_parameter($param);
    return $get;
  }
}
