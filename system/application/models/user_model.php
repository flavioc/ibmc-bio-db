<?php

class User_model extends BioModel
{
  function User_model()
  {
    parent::BioModel('user');
  }

  function has_user($id)
  {
    return $this->has_id($id);
  }

  function validate($name, $pwd)
  {
    $this->db->where('enabled', TRUE);
    $user = $this->get_row('name', $name);

    if(!$user) {
      return false;
    }

    return $user['password'] == md5($pwd);
  }

  function _get_user($key, $value)
  {
    $this->db->where('enabled', TRUE);
    $data = $this->get_row($key, $value);

    if(!$data) {
      return null;
    }

    if($data['birthday']) {
      $this->load->helper('date_utils');
      $data['birthday'] = convert_sql_date_to_html($data['birthday']);
    }

    return $data;
  }

  function get_user_by_name($name)
  {
    return $this->_get_user('name', $name);
  }

  function get_user_by_id($id)
  {
    return $this->_get_user('id', $id);
  }

  function _get_user_image($key, $value)
  {
    $this->load->helper('image_utils');

    $this->db->select('image');
    $this->db->where('image IS NOT NULL');
    $this->db->where('enabled', TRUE);
    $array = $this->get_row($key, $value);

    return process_db_image($array['image']);
  }

  function get_user_image_by_id($id)
  {
    return $this->_get_user_image('id', $id);
  }

  function get_user_image_by_name($name)
  {
    return $this->_get_user_image('name', $name);
  }

  function user_exists($name)
  {
    return $this->get_user_by_name($name) != null;
  }

  function username_used($name)
  {
    $this->db->select('id');
    $row = $this->get_row('name', $name);

    return $row != null;
  }

  function new_user($name, $complete_name, $email,
    $birthday, $password, $image)
  {
    $data = array(
      'name' => $name,
      'email' => $email,
      'password' => $password,
    );

    if($complete_name != null) {
      $data['complete_name'] = $complete_name;
    }

    if($birthday != null) {
      $this->load->helper('date_utils');
      $data['birthday'] = convert_html_date_to_sql($birthday);
    }

    if($image != null) {
      $data['image'] = $image;
    }

    return $this->insert_data_with_history($data);
  }

  function edit_user($id, $complete_name, $email, $birthday,
    $imagecontent, $new_password)
  {
    $data = array(
      'complete_name' => $complete_name,
      'email' => $email,
    );

    if($birthday) {
      $this->load->helper('date_utils');
      $data['birthday'] = convert_html_date_to_sql($birthday);
    }

    if($imagecontent) {
      $data['image'] = $imagecontent;
    }

    if($new_password && count($new_password) > 0) {
      $data['password'] = $new_password;
    }

    return $this->edit_data_with_history($id, $data);
  }

  function get_users()
  {
    $this->db->where('enabled', TRUE);

    return $this->get_rows('user_type', 'user');
  }

  function get_users_all()
  {
    $this->db->select('id, name');
    return $this->get_all();
  }

  function delete_user($id)
  {
    return $this->edit_data_with_history($id, array('enabled' => FALSE));
  }
}
