<?php

class Admin extends BioController
{
  function Admin()
  {
    parent::BioController();
  }
  
  public function drop_database()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->assign('title', 'Database reset');
    
    $this->smarty->view('admin/drop_database');
  }
  
  public function drop2()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->assign('title', 'WARNING: Database reset');
    
    $this->smarty->view('admin/drop2');
  }
  
  public function do_drop()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->load->model('taxonomy_tree_model');
    $this->load->model('taxonomy_rank_model');
    $this->load->model('label_model');
    $this->load->model('sequence_model');
    $this->load->model('user_model');
    
    $this->taxonomy_tree_model->delete_all_custom();
    $this->taxonomy_rank_model->delete_all_custom();
    $this->label_model->delete_all_custom();
    $this->sequence_model->delete_all();
    $this->user_model->delete_all_users();
    
    $this->smarty->assign('title', 'Database reset');
    $this->smarty->view('admin/do_drop');
  }
  
  public function export_database()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="database.xml"');
    
    echo "<biodata>\n";
    
    // labels
    $this->load->model('label_model');
    $this->load->helper('label_exporter');
    echo export_labels_xml($this->label_model->get_all(), 1);
  
    // taxonomy trees
    $this->load->model('taxonomy_tree_model');
    $this->load->model('taxonomy_model');
    $this->load->helper('tree_exporter');
    $tree_ids = $this->taxonomy_tree_model->get_trees(array('no_ncbi' => true), array(), 'tree_id AS id');
    echo export_trees_xml($this->taxonomy_tree_model, $this->taxonomy_model, $tree_ids, 1);
    
    // sequences
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
    $this->load->helper('exporter');
    $sequences = $this->sequence_model->get_all(null, null, array(), array(), 'id, name, content');
    $seq_labels = $this->label_sequence_model->get_sequences($sequences);
    echo export_sequences_xml($sequences, $seq_labels, $this->username, 'all sequences', 1);
    
    echo "\n</biodata>";
  }
}
?>