<?php

class Welcome extends BioController {

  function Welcome()
  {
    parent::BioController();	
    $this->load->model('user_model');
  }
	
  function index()
  {
    $this->smarty->assign('title', 'Main');

    // load comment
    $this->load->model('comment_model');
    $this->load->helper('text');
    $comment = newline_tab_html($this->comment_model->get());
    $this->smarty->assign('comment', $comment);

    if(!$this->logged_in) {
      $this->smarty->load_scripts(VALIDATE_SCRIPT);
      $this->smarty->fetch_form_row('login_username');
      $this->smarty->fetch_form_row('login_password');

      $redirect = $this->get_parameter('redirect');

      if($redirect) {
        $this->smarty->assign('redirect', $redirect);
      }
    } else {
      $userdata = $this->user_model->get_user_by_id($this->user_id);

      $this->smarty->assign('complete_name', $userdata['complete_name']);
      $this->smarty->assign('email', $userdata['email']);
      $this->smarty->assign('birthday', $userdata['birthday']);
    }

    $this->smarty->view('welcome_message');
  }

  function login()
  {
    if($this->logged_in) {
      redirect('');
      return;
    }

    $this->load->library('form_validation');

    // form rules
    $this->form_validation->set_username_rule('login_username');
    $this->form_validation->set_password_rule('login_password');

    // validate data
    if($this->form_validation->run() == false) {
      $this->assign_row_data('login_username');
      $this->assign_row_data('login_password', false);
      redirect('welcome/index');
    } else {
      $username = $this->get_post('login_username');
      $password = $this->get_post('login_password');

      if($this->user_model->validate($username, $password)) {
        // everything's fine.
        $this->do_login($username);
        redirect($this->get_post('redirect'));
      } else {
        $this->set_form_value('login_username');
        if($this->user_model->user_exists($username)) {
          $this->set_form_error('login_password', 'The password is wrong.');
        } else {
          $this->set_form_error('login_username', 'User does not exist.');
        }

        redirect('');
      }
    }
  }

  function do_login($username)
  {
    $user = $this->user_model->get_user_by_name($username);
    $this->session->set_userdata('user_id', $user['id']);
    $this->session->set_userdata('username', $username);
    $this->session->set_userdata('logged_in', true);
    $this->session->set_userdata('user_type', $user['user_type']);
  }

  function logout()
  {
    if(!$this->logged_in) {
      // not logged in
      return;
    }

    $this->session->unset_userdata('logged_in');
    $this->session->unset_userdata('username');
    $this->session->unset_userdata('user_type');
    redirect('');
  }
}
