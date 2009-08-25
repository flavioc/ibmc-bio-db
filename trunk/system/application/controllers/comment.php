<?php

class Comment extends BioController
{ 
  function Comment()
  {
    parent::BioController();
    $this->load->model('comment_model');
  }

  public function edit()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $comment = $this->comment_model->get();
    $this->smarty->assign('comment', $comment);

    $this->smarty->assign('title', 'Edit comment');
    $this->smarty->view('comment/edit');
  }

  public function do_edit()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $comment = $this->get_post('comment');

    $this->comment_model->set($comment);

    $this->set_info_message("Comment saved");
    redirect('');
  }
}