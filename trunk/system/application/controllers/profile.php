<?php

class Profile extends BioController {

  function Profile()
  {
    parent::BioController();	
    $this->load->model('user_model');
  }

  function view($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $user = $this->user_model->get_user_by_id($id);
    $this->smarty->assign('user', $user);

    if($user) {
      $this->smarty->assign('title', 'User ' . $user['name']);
    } else {
      $this->smarty->assign('title', 'Invalid user');
    }

    $this->smarty->view('view_user');
  }

  function list_all()
  {
    if(!$this->is_admin) {
      return;
    }

    $this->smarty->assign('title', 'User list');
    $this->smarty->load_scripts(CONFIRM_SCRIPT);
    $users = $this->user_model->get_users();

    $this->smarty->assign('users', $users);
    $this->smarty->view('user_list');
  }

  function do_delete($id)
  {
    if(!$this->is_admin) {
      echo "error";
      return;
    }

    $this->user_model->delete_user($id);

    echo "ok";
  }

  function edit()
  {
    $this->smarty->assign('title', 'Edit profile');

    $this->smarty->load_scripts(DATEPICKER_SCRIPT, VALIDATE_SCRIPT);
    $this->smarty->load_stylesheets(DATEPICKER_THEME);

    $userdata = $this->user_model->get_user_by_id($this->user_id);

    $this->smarty->fetch_form_row('old_password');
    $this->smarty->fetch_form_row('complete_name', $userdata['complete_name']);
    $this->smarty->fetch_form_row('email', $userdata['email']);
    $this->smarty->fetch_form_row('birthday', $userdata['birthday']);
    $this->smarty->fetch_form_row('password1');
    $this->smarty->fetch_form_row('password2');
    $this->smarty->fetch_form_row('image');

    $this->smarty->view('edit_user');
  }

  function do_edit()
  {
    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_password_rule('old_password');
    $this->form_validation->set_complete_name_rule();
    $this->form_validation->set_email_rule();
    $this->form_validation->set_date_rule('birthday', 'Birthday');
    $this->form_validation->set_password_rule('password1', false);
    $this->form_validation->set_password_retype_rule('password2', 'password1', false);

    if($this->form_validation->run() == FALSE) {
      $errors = true;
      $this->assign_row_data('complete_name');
      $this->assign_row_data('email');
      $this->assign_row_data('birthday');
      $this->assign_row_data('old_password', false);
      $this->assign_row_data('password1', false);
      $this->assign_row_data('password2', false);
    }

    $password = $this->get_post('old_password');
    if(!$this->user_model->validate($this->username, $password)) {
      $this->set_form_error('old_password', 'Password is not valid.');
      $errors = true;
    }

    $image_str = $this->get_post_filename('image'); 
    $image_data = null;
    if($image_str) {
      // verify image upload
      $this->load->library('upload', $this->_get_upload_config());
      $upload_ret = $this->upload->do_upload("image");

      if(!$upload_ret) {
        $this->set_upload_form_error('image');
        $errors = true;
      }

      $image_data = $this->upload->data();
    }

    if($errors) {
      redirect('profile/edit');
    } else {
      $complete_name = $this->get_post('complete_name');
      $email = $this->get_post('email');
      $birthday = $this->get_post('birthday');
      $new_password = $this->get_post('password1');
      $imagecontent = null;

      if($image_data) {
        $this->load->helper("image_utils");
        process_user_image($this, $image_data);
        $imagecontent = read_file_and_delete($image_data);
      }

      $this->user_model->edit_user($this->user_id, $complete_name,
        $email, $birthday, $imagecontent, $new_password);

      redirect('');
    }
  }
	
	function register()
  {
    $this->smarty->assign('title', 'Register');

    $this->smarty->load_scripts(DATEPICKER_SCRIPT, VALIDATE_SCRIPT);
    $this->smarty->load_stylesheets(DATEPICKER_THEME);

    $this->smarty->fetch_form_row('username');
    $this->smarty->fetch_form_row('complete_name');
    $this->smarty->fetch_form_row('email');
    $this->smarty->fetch_form_row('birthday');
    $this->smarty->fetch_form_row('password1');
    $this->smarty->fetch_form_row('password2');
    $this->smarty->fetch_form_row('image');

    $this->smarty->view('register');
	}

  function _get_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'gif|jpg|png|bmp';
    $config['max_size'] = '400';
    $config['max_width'] = '1024';
    $config['max_height'] = '768';

    return $config;
  }

  function do_register()
  {
    $errors = false;

    $this->load->library('input');
    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_username_rule();
    $this->form_validation->set_complete_name_rule();
    $this->form_validation->set_email_rule();
    $this->form_validation->set_date_rule('birthday', 'Birthday');
    $this->form_validation->set_password_rule('password1');
    $this->form_validation->set_password_retype_rule('password2', 'password1');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
      $this->assign_row_data('username');
      $this->assign_row_data('complete_name');
      $this->assign_row_data('email');
      $this->assign_row_data('birthday');
      $this->assign_row_data('password1', false);
      $this->assign_row_data('password2', false);
    }

    $username = $this->get_post('username');
    if($this->user_model->user_exists($username)) {
      $this->set_form_error('username', 'Username ' . $username . ' is already taken.');
      $errors = true;
    }

    $image_str = $this->get_post_filename('image'); 
    if($image_str) {
      // verify image upload
      $this->load->library('upload', $this->_get_upload_config());
      $upload_ret = $this->upload->do_upload("image");

      if(!$upload_ret) {
        $this->set_upload_form_error('image');
        $errors = true;
      }

      $image_data = $this->upload->data();
    }

    if($errors) {
      redirect('profile/register');
    } else {
      $complete_name = $this->get_post('complete_name');
      $email = $this->get_post('email');
      $birthday = $this->get_post('birthday');
      $password = $this->get_post('password1');

      if($image_data) {
        $this->load->helper("image_utils");
        process_user_image($this, $image_data);
        $imagecontent = read_file_and_delete($image_data);
      }

      $this->user_model->new_user($username, $complete_name, $email,
        $birthday, $password, $imagecontent);

      redirect('');
    }
  }
}
