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
  
  public function search()
  {
    $this->load->plugin('parser');
    
    $search = urldecode($this->get_parameter('search_global'));
    $search = stripslashes($search);
    $this->session->set_userdata('search_term', $search);
    
    try {
      $parser = new Parser($search);
      $tree = $parser->parse();
      $tree_json = json_encode($tree);
      $this->session->set_rawcookie('saved_search_tree', $tree_json, time()+500);
      redirect('search?type=search');
    } catch(Exception $e) {
      $search = rawurlencode($search);
      redirect("wide_search/general_search?search=$search&error=" . rawurlencode($e->getMessage()));
    }
  }
  
  public function general_search()
  {
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