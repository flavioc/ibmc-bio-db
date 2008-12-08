<?php

class User_model extends Model
{
  function User_model()
  {
    parent::Model();
  }

  function validate($name, $pwd)
  {
    $query = $this->db->get_where('user', array('name' => $name));

    if($query->num_rows() != 1) {
      return false;
    }

    $user = $query->row();

    return $user->password == md5($pwd);
  }

  function _get_user($key, $value)
  {
    $query = $this->db->get_where('user', array($key => $value));

    if($query->num_rows() != 1) {
      return null;
    }

    $data = $query->row_array();

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
    $this->db->where($key, $value);
    $query = $this->db->get('user');

    if($query->num_rows() != 1) {
      return null;
    }

    $array = $query->row_array();

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
    $result = $this->get_user_by_name($name);

    return $result != null;
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

    // send email
    $this->load->library('email');

    $this->email->from('changeThis@evolution.ibmc.up.pt', 'BioDB');
    $this->email->to($email);

    $this->email->subject('Registo bem sucedido');
    $this->email->message("Olá,

O seu registo foi bem sucedido.

Username: $name
Password: $password

Cumprimentos.");

    $this->email->send();

    $this->db->insert('user', $data);
  }

  function edit_user($id, $complete_name, $email, $birthday,
    $imagecontent, $new_password)
  {
    $this->db->where('id', $id);
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

    $this->db->update('user', $data);
  }

  function get_users()
  {
    $this->db->where('user_type', 'user');

    return $this->db->get('user', 10)->result_array();
  }

  function delete_user($id)
  {
    $this->db->delete('user', array('id' => $id)); 
  }
}
