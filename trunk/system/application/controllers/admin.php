<?php

class Admin extends BioController
{
  private $import_error_message = null;
  
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
  
  public function import_database()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->smarty->assign('title', 'Import database');
    
    $this->smarty->view('admin/import');
  }
  
  private function __import_database($file)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      $this->import_error_message = "Error parsing the xml file";
      return null;
    }

    $top = $xmlDoc->documentElement;
    
    if(!$top || $top->nodeName != 'biodata') {
      $this->import_error_message = "Xml file has no top level node biodata";
      return null;
    }
    
    $labels = find_xml_child($top, 'labels');
    if(!$labels) {
      $this->import_error_message = "Missing labels node";
      return null;
    }
    
    $trees = find_xml_child($top, 'trees');
    if(!$trees) {
      $this->import_error_message = "Missing trees node";
      return null;
    }
    
    $sequences = find_xml_child($top, 'sequences');
    if(!$sequences) {
      $this->import_error_message = "Missing sequences node";
      return null;
    }
    
    // import labels
    $this->load->helper('label_importer');
    $this->load->model('label_model');
    $label_data = import_label_xml_node($labels, $this->label_model);
    $this->smarty->assign('labels', $label_data);
    
    // import taxonomy trees
    $this->load->model('taxonomy_rank_model');
    $this->load->model('taxonomy_tree_model');
    $this->load->model('taxonomy_model');
    $this->load->helper('tree_importer');
    $this->smarty->assign('trees',
      import_trees_xml_node($trees, $this->taxonomy_tree_model, $this->taxonomy_rank_model, $this->taxonomy_model));
    
    // import sequences
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
    $this->load->helper('xml_importer');
    $this->load->library('ImportInfo');
    $info = import_sequences_xml_node($sequences, $this);
    list($seqs, $imported_seq_labels) = $info->import();
    $this->smarty->assign('sequences', $seqs);
    
    return true;
  }
  
  public function do_import_database()
  {
    if(!$this->is_admin) {
      return $this->invalid_permission_admin();
    }
    
    $this->load->library('upload', $this->__get_xml_upload_config());

    $upload_ret = $this->upload->do_upload('file');

    if($upload_ret) {
      $data = $this->upload->data();
      
      $file = $data['full_path'];
      
      $ret = $this->__import_database($file);
      
      unlink($file);
      
      if(!$ret) {
        $this->set_form_error('file', $this->import_error_message);
        redirect('admin/import_database');
      } else {
        $this->smarty->assign('title', 'Import database');
        $this->use_mygrid();
        $this->smarty->view('admin/do_import');
      }
    } else {
      $this->set_upload_form_error('file');
      redirect('admin/import_database');
    }
  }
}
?>