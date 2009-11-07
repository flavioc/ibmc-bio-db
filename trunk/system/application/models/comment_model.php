<?php

class Comment_model extends BioModel
{
  private static $root_user = 0;
  
  function Comment_model()
  {
    parent::BioModel('configuration');
    $this->load->model('configuration_model');
  }

  public function get()
  {
    return $this->configuration_model->get_key('comment', self::$root_user);
  }

  public function set($comment)
  {
    return $this->configuration_model->set_key('comment', $comment, self::$root_user);
  }
}
