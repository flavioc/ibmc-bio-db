<?php

class User_model extends BioModel
{
  function User_model()
  {
    parent::BioModel('user');
  }

  public function has_user($id)
  {
    return $this->has_id($id);
  }
  
  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }
  
  public function update_access($id)
  {
    $this->db->set('last_access', 'NOW()', false);
    
    return $this->edit_data($id);
  }

  public function validate($name, $pwd)
  {
    $this->db->where('enabled', TRUE);
    $user = $this->get_row('name', $name);

    if(!$user) {
      return false;
    }

    return $user['password'] == md5($pwd);
  }

  private function _get_user($key, $value)
  {
    $this->db->where('enabled', TRUE);
    $data = $this->get_row($key, $value);

    if(!$data) {
      return null;
    }

    return $data;
  }

  public function get_user_by_name($name)
  {
    return $this->_get_user('name', $name);
  }

  public function get_user_by_id($id)
  {
    return $this->_get_user('id', $id);
  }

  public function user_exists($name)
  {
    return $this->get_user_by_name($name) != null;
  }

  public function username_used($name)
  {
    $this->db->select('id');
    $row = $this->get_row('name', $name);

    return $row != null;
  }

  public function new_user($name, $complete_name, $email,
    $password)
  {
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 32 || $this->username_used($name)) {
      return false;
    }
    
    $email = trim($email);
    if(strlen($email) <= 0 || strlen($email) > 128) {
      return false;
    }
    
    $password = trim($password);
    if(strlen($password) < 6 || strlen($password) > 32) {
      return false;
    }
    
    $data = array(
      'name' => $name,
      'email' => $email,
      'password' => $password,
    );

    if($complete_name != null) {
      $data['complete_name'] = trim($complete_name);
      
      if(strlen($data['complete_name']) > 512) {
        return false;
      }
    }

    return $this->insert_data_with_history($data);
  }

  public function edit_user($id, $complete_name, $email,
    $new_password)
  {
    $complete_name = trim($complete_name);
    if(strlen($complete_name) > 512) {
      return false;
    }
    
    $email = trim($email);
    if(strlen($email) <= 0 || strlen($email) > 128) {
      return false;
    }
    
    $data = array(
      'complete_name' => $complete_name,
      'email' => $email,
    );

    $new_password = trim($new_password);
    if($new_password && strlen($new_password) > 0) {
      if(strlen($new_password) < 6 || strlen($new_password) > 32) {
        return false;
      }
      $data['password'] = $new_password;
    }

    return $this->edit_data_with_history($id, $data);
  }
  
  private function __get_all_select()
  {
    $this->db->select('id, name, complete_name, email, user_type, last_access');
  }

  /* only non admin users */
  public function get_users()
  {
    $this->__get_all_select();
    $this->db->where('enabled', TRUE);

    return $this->get_rows('user_type', 'user');
  }
  
  /* get active users */
  public function get_users_active()
  {
    $this->__get_all_select();
    $this->db->where('enabled', TRUE);
    
    return $this->get_all();
  }

  public function get_users_all()
  {
    $this->__get_all_select();
    $this->db->select('id, name');
    return $this->get_all();
  }

  public function delete_user($id)
  {
    return $this->edit_data_with_history($id, array('enabled' => FALSE));
  }
  
  public function delete_all_users()
  {
    $this->db->where("user_type = 'user'");
    return $this->delete_rows();
  }
}