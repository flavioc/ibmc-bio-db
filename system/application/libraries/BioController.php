<?php

class BioController extends Controller
{
  var $smarty;

  function BioController()
  {
    parent::__construct();
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

    $this->smarty->load_scripts(JSON_SCRIPT, SESSION_SCRIPT);
  }

  function use_paging_size()
  {
    if($this->logged_in) {
      $this->load->model('configuration_model');
      $this->smarty->assign('paging_size', $this->configuration_model->get_paging_size());
    }
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
}
