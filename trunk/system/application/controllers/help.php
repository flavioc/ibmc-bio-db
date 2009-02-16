<?php

class Help extends BioController {
  
  function Help() {
    parent::BioController();
  }

  function index()
  {
  }

  function settings()
  {
    $this->smarty->view_s('help/settings');
  }
}

