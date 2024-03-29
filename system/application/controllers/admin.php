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
    $this->load->model('file_model');
    
    $this->taxonomy_tree_model->delete_all_custom();
    $this->taxonomy_rank_model->delete_all_custom();
    $this->label_model->delete_all_custom();
    $this->sequence_model->delete_all();
    $this->user_model->delete_all_users();
    $this->file_model->delete_all_labels();
    
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
    $this->load->library('LabelExporter');
    echo $this->labelexporter->export_group($this->label_model->get_all(), 1);
    
    // ranks
    $this->load->model('taxonomy_rank_model');
    $this->load->library('RankExporter');
    echo $this->rankexporter->export_group($this->taxonomy_rank_model->get_ranks(), 1);
  
    // taxonomy trees
    $this->load->library('TreeExporter');
    $tree_ids = $this->taxonomy_tree_model->get_trees(array('no_ncbi' => true), array(), 'tree_id AS id');
    echo $this->treeexporter->export_group($tree_ids, 1);
    
    // sequences
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
    $this->load->library('SequenceExporter');
    
    $sequences = $this->sequence_model->get_all(null, null, array(), array(), 'id, name, content');
    $seq_labels = $this->label_sequence_model->get_sequences($sequences);
    echo $this->sequenceexporter->export_xml($sequences, $seq_labels, $this->username, 'all sequences', 1);
    
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
    
    $ranks = find_xml_child($top, 'ranks');
    
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
    $this->load->library('LabelImporter');
    $label_data = $this->labelimporter->import_xml_node($labels);
    $this->smarty->assign('labels', $label_data);
    
    // import ranks
    if($ranks) {
      $this->load->library('RankImporter');
      $rank_data = $this->rankimporter->import_xml_node($ranks);
      $this->smarty->assign('ranks', $rank_data);
    } else {
      $this->smarty->assign('ranks', null);
    }
    
    // import taxonomy trees
    $this->load->library('TreeImporter');
    $this->smarty->assign('trees',
      $this->treeimporter->import_group_xml_node($trees));
    
    // import sequences
    $this->load->library('SequenceImporter');
    $info = $this->sequenceimporter->import_xml_node($sequences);
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
  
  public function change_background()
  {
    if(!$this->is_admin)
      return $this->invalid_permission_admin();
    
    $this->smarty->fetch_form_row('file');
    $this->smarty->assign('title', 'Database background');
    $this->smarty->view('admin/background');
  }
  
  public function do_change_background()
  {
    if(!$this->is_admin)
      return $this->invalid_permission_admin();
      
    $name = $this->get_post('submit');
    $this->load->model('file_model');
   
    if($name == 'Remove current') {
      $this->file_model->remove_background();
      redirect('admin/change_background');
    }
    
    $this->load->library('upload', $this->__get_background_upload_config());
    
    $upload_ret = $this->upload->do_upload('file');

    if(!$upload_ret) {
      $this->set_upload_form_error('file');
      redirect('admin/change_background');
      return;
    }
    
    $data = $this->upload->data();
    $path = $data['full_path'];
    
    $content = file_get_contents($path);
    
    unlink($path);
    
    $type = file_extension($path);
    if($type != 'png' && $type != 'jpg') {
      $this->set_form_error('file', 'Must be a PNG or JPG file');
      redirect('admin/change_background');
      return;
    }
    
    
    $this->file_model->add_background($content, $type);
    redirect('admin/change_background');
  }
  
  private function __get_background_upload_config()
  {
    $config['upload_path'] = UPLOAD_DIRECTORY;
    $config['overwrite'] = true;
    $config['encrypt_name'] = true;
    $config['allowed_types'] = 'jpg|png';

    return $config;
  }
}

?>