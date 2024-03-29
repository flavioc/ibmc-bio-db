<?php

class Profile extends BioController
{
  function Profile()
  {
    parent::BioController();	
    $this->load->model('user_model');
  }

  private function __view($id)
  {
    $this->smarty->assign('id', $id);
    
    if(!$this->user_model->has_user($id)) {   
      $this->smarty->assign('title', 'User not found');
      $this->smarty->view('profile/not_found');
      return;
    }

    $user = $this->user_model->get_user_by_id($id);
    $this->smarty->assign('user', $user);

    if($user) {
      $this->smarty->assign('title', 'User ' . $user['name']);
    } else {
      $this->smarty->assign('title', 'Invalid user');
    }

    if($this->is_admin) {
      $this->use_impromptu();
    }

    $this->smarty->view('profile/view');
  }

  public function view($id)
  {
    $this->__view($id);
  }

  public function view_self()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->__view($this->user_id);
  }

  public function list_all()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }

    $this->smarty->assign('title', 'User list');
    $this->use_mygrid();

    $this->smarty->view('profile/list');
  }

  public function get_all()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_empty();
    }

    $users = $this->user_model->get_users_active();

    $this->json_return($users);
  }

  public function delete_redirect()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_false();
    }

    $id = $this->get_post('id');
    $this->user_model->delete_user($id);

    redirect('profile/list_all');
  }

  public function delete_dialog($id)
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_nothing();
    }

    $user = $this->user_model->get_user_by_id($id);
    $this->smarty->assign('user', $user);

    $this->smarty->view_s('profile/delete');
  }

  public function edit($id)
  {
    if(!$this->__can_edit_profile($id)) {
      return $this->invalid_permission();
    }
    
    if(!$this->user_model->has_user($id)) {
      $this->smarty->assign('id', $id);
      $this->smarty->assign('title', 'User not found');
      $this->smarty->view('profile/not_found');
      return;
    }

    $this->smarty->assign('title', 'Edit profile');

    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $userdata = $this->user_model->get_user_by_id($id);
    $this->smarty->assign('user', $userdata);

    $this->smarty->fetch_form_row('old_password');
    $this->smarty->fetch_form_row('complete_name', $userdata['complete_name']);
    $this->smarty->fetch_form_row('email', $userdata['email']);
    $this->smarty->fetch_form_row('password1');
    $this->smarty->fetch_form_row('password2');

    $this->smarty->view('profile/edit');
  }
  
  private function __can_edit_profile($id)
  {
    return ($this->logged_in && $this->user_id == $id) || $this->is_admin;
  }

  public function do_edit()
  {
    $id = $this->get_post('id');
    
    if(!$this->__can_edit_profile($id)) {
      return $this->invalid_permission();
    }
    
    if(!$this->user_model->has_user($id)) {
      $this->smarty->assign('id', $id);
      $this->smarty->assign('title', 'User not found');
      $this->smarty->view('profile/not_found');
      return;
    }

    $errors = false;

    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_password_rule('old_password');
    $this->form_validation->set_complete_name_rule();
    $this->form_validation->set_email_rule();
    $this->form_validation->set_password_rule('password1', false);
    $this->form_validation->set_password_retype_rule('password2', 'password1', false);

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    $password = $this->get_post('old_password');
    if(!$this->user_model->validate($this->username, $password)) {
      $this->set_form_error('old_password', 'Password is not valid.');
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('complete_name');
      $this->assign_row_data('email');
      $this->assign_row_data('old_password', false);
      $this->assign_row_data('password1', false);
      $this->assign_row_data('password2', false);
      redirect("profile/edit/$id");
    } else {
      $complete_name = $this->get_post('complete_name');
      $email = $this->get_post('email');
      $new_password = $this->get_post('password1');
      
      $this->user_model->edit_user($id, $complete_name,
        $email, $new_password);

      $this->set_info_message("Profile has been saved");
      redirect("profile/view/$id");
    }
  }

  public function settings()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Edit settings');
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    $this->smarty->load_scripts(FORM_SCRIPT);
    $this->load->model('configuration_model');

    $this->smarty->fetch_form_row('paging_size', $this->configuration_model->get_paging_size());

    $this->smarty->view('profile/settings');
  }

  public function edit_settings()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $errors = false;

    $this->load->library('form_validation');
    $this->load->model('configuration_model');

    $this->form_validation->set_rules('paging_size', 'Paging size', 'trim|required|numeric');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
      $this->assign_row_data('paging_size');
    }

    $paging_size = intval($this->get_post('paging_size'));

    if(!$errors) {
      if($paging_size < 1 || $paging_size > 1000) {
        $this->set_form_error('paging_size', 'Paging size must be between 1 and 1000');
        $errors = true;
      }
    }

    if($errors) {
      redirect('profile/settings');
    } else {
      $this->configuration_model->set_paging_size($paging_size);
      $this->set_paging_size_cookie($paging_size);

      $this->set_info_message("Settings have been saved");
      redirect('profile/view_self');
    }
  }
	
	public function register()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }

    $this->smarty->assign('title', 'Register');

    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('username');
    $this->smarty->fetch_form_row('complete_name');
    $this->smarty->fetch_form_row('email');
    $this->smarty->fetch_form_row('password1');
    $this->smarty->fetch_form_row('password2');

    $this->smarty->view('profile/register');
	}

  private function __get_upload_config()
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

  public function do_register()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }

    $errors = false;

    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_username_rule();
    $this->form_validation->set_complete_name_rule();
    $this->form_validation->set_email_rule();
    $this->form_validation->set_password_rule('password1');
    $this->form_validation->set_password_retype_rule('password2', 'password1');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    $username = $this->get_post('username');
    if($this->user_model->username_used($username)) {
      $this->set_form_error('username', 'Username ' . $username . ' is already taken.');
      $errors = true;
    }

    if($errors) {
      $this->assign_row_data('username');
      $this->assign_row_data('complete_name');
      $this->assign_row_data('email');
      $this->assign_row_data('password1', false);
      $this->assign_row_data('password2', false);
      redirect('profile/register');
    } else {
      $complete_name = $this->get_post('complete_name');
      $email = $this->get_post('email');
      $password = $this->get_post('password1');

      $this->user_model->new_user($username, $complete_name, $email,
        $password);
      
      $this->set_info_message("Registered user $username");

      redirect('profile/list_all');
    }
  }
}