<?php

class Wide_Search extends BioController
{
  function Wide_Search()
  {
    parent::BioController();
    $this->load->model('label_model');
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_rank_model');
  }
  
  function search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->load->library('Tokenizer');
    $this->load->library('Parser');
    
    $search = stripslashes($this->get_parameter('search'));
    $this->session->set_userdata('search_term', $search);
    
    try {
      $parser = new Parser($this, $search);
      $tree = $parser->parse();
      $tree_json = json_encode($tree);
      redirect('sequence/search?type=search&term=' . rawurlencode($tree_json));
    } catch(Exception $e) {
      $search = rawurlencode($search);
      redirect("wide_search/general_search?search=$search&error=" . rawurlencode($e->getMessage()));
    }
  }
  
  function general_search()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->use_mygrid();
    
    $search = htmlspecialchars(rawurldecode($this->get_parameter('search')), ENT_QUOTES);
    $error = htmlspecialchars(rawurldecode($this->get_parameter('error')), ENT_QUOTES);
    
    $labels = $this->label_model->get_all(0, 1, array('name' => $search));
    $ranks = $this->taxonomy_rank_model->get_ranks(1, 0, array('name' => $search));
    $taxonomies = $this->taxonomy_model->search_total($search, null, null);
    $sequences = $this->sequence_model->get_all(0, 1, array('name' => $search));
    
    $this->smarty->assign('labels', $labels);
    $this->smarty->assign('ranks', $ranks);
    $this->smarty->assign('taxonomies', $taxonomies);
    $this->smarty->assign('sequences', $sequences);
    
    $this->smarty->assign('error', $error);
    $this->smarty->assign('search', $search);
    
    $this->smarty->assign('title', 'Search by "' . $search . '"');
    
    $this->smarty->view('wide_search/view');
  }
}