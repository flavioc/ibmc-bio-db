<?php

class BioForm_validation extends CI_Form_validation
{
  private $base_password_rules = 'trim|min_length[6]|max_length[32]';
  private $base_username_rules = 'trim|required|min_length[2]|max_length[32]';

  function BioForm_validation()
  {
    parent::__construct();
    // remove unneeded delimiters
    $this->set_error_delimiters('', '');
  }

  public function date_check($date)
  {
    $vec = explode('-', $date);

    if(count($vec) != 3) {
      return false;
    }

    $day = (int)$vec[0];
    $month = (int)$vec[1];
    $year = (int)$vec[2];

    return checkdate($month, $day, $year);
  }

  public function set_username_rule($what = 'username')
  {
    $this->set_rules($what, 'Username', $this->base_username_rules);
  }

  public function set_complete_name_rule($what = 'complete_name')
  {
    $this->set_rules($what, 'Complete name', 'trim|max_length[512]');
  }

  public function set_password_rule($what = 'password', $required = true)
  {
    $rules = $this->base_password_rules;

    if($required) {
      $rules .= '|required';
    }

    $this->set_rules($what, 'Password', $rules);
  }

  public function set_password_retype_rule($what, $other, $required = true)
  {
    $rules = $this->base_password_rules . '|matches[' . $other . ']';

    if($required) {
      $rules .= '|required';
    }

    $this->set_rules($what, 'Retyped password', $rules);
  }
  
  public function set_email_rule($what = 'email')
  {
    $this->set_rules($what, 'Email', 'trim|required|valid_email|max_length[128]');
  }

  public function set_date_rule($what, $msg)
  {
    $this->set_rules($what, $msg, 'trim|date_check');
  }
}
