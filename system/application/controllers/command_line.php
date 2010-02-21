<?php

class Command_Line extends BioController
{
  function Command_Line()
  {
    if(!array_key_exists('IS_SCRIPT', $_SERVER)) {
      redirect('');
    }
    parent::BioController();
    
    $this->load->model('user_model');
    
    $admin = $this->user_model->get_user_by_name('admin');
    if(!$admin) {
      throw new Exception("No admin user found");
    }
    
    $this->logged_in = true;
    $this->username = 'admin';
    $this->user_type = 'admin';
    $this->user_id = $admin['id'];
    $this->is_admin = true;
    
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
    $this->load->model('label_model');
    $this->load->model('taxonomy_model');
    $this->load->model('taxonomy_rank_model');
    $this->load->model('taxonomy_tree_model');
    $this->load->model('search_model');
  }
  
  public function search($str, $transform_label = null)
  {
    try {
      $this->load->plugin('parser');
      
      $parser = new Parser($str);
      $tree = $parser->parse();
      
      if($transform_label) {
        $label_id = $this->label_model->get_id_by_name($transform_label);
      } else {
        $label_id = null;
      }
      
      $results = $this->search_model->get_search($tree, array('transform' => $label_id, 'select' => 'id, name, content'));
      
      echo count($results) . " TOTAL\n";
      foreach($results as &$result) {
        $id = $result['id'];
        $name = $result['name'];
        $content = $result['content'];
        
        echo "$id - $name - $content\n";
      }
      
    } catch(Exception $e) {
      echo "Error parsing $str: ".$e->getMessage() . "\n";
      return 1;
    }
  }
  
  private function __get_file($file)
  {
    return str_replace('FILE_SEPARATOR', '/', $file);
  }
  
  public function import_and_link($file1, $file2)
  {
    $file1 = $this->__get_file($file1);
    $file2 = $this->__get_file($file2);
    
    if(!file_exists($file1)) {
      echo "File $file1 doesn't exist\n";
      return;
    }
    if(!file_exists($file2)) {
      echo "File $file2 doesn't exist\n";
      return;
    }
    
    $this->load->library('SequenceImporter');
    
    $info1 = $this->sequenceimporter->import_file($file1);
    if(!$info1) {
      echo "Invalid sequence file: $file1\n";
      return;
    }
    
    if(!$info1->all_dna()) {
      echo "File $file1 must only contain DNA sequences\n";
      return;
    }
    
    $info2 = $this->sequenceimporter->import_file($file2);
    if(!$info2) {
      echo "Invalid sequence file: $file2\n";
      return;
    }
    
    if(!$info2->all_protein()) {
      echo "File $file2 must only contain protein sequences\n";
      return;
    }
    
    if(!$info1->duo_match($info2)) {
      echo "$file1 and $file2 must contain the same number of sequences\n";
      return;
    }
    
    $this->__show_two_seq_files($info1, $info2);
  }
  
  public function import_sequence_file_and_generate($file)
  {
    $file = $this->__get_file($file);
    if(!file_exists($file)) {
      echo "File $file doesn't exist\n";
      return;
    }
    
    $this->load->library('SequenceImporter');
    
    $info = $this->sequenceimporter->import_file($file);
    if(!$info) {
      echo "Invalid sequence file: $file\n";
      return;
    }
    
    if(!$info->all_dna()) {
      echo "File $file must only contain DNA sequences\n";
      return;
    }
    
    $file2 = $info->convert_protein_file();
    if(!$file2) {
      echo "Couldn't generate protein file\n";
      return;
    }
    
    $info2 = $this->sequenceimporter->import_file($file2);
    if(!$info2) {
      echo "Couldn't import protein sequences from $file2";
      unlink($file2);
      return;
    }
    
    unlink($file2);
    
    $this->__show_two_seq_files($info, $info2);
  }
  
  private function __show_two_seq_files(&$info1, &$info2)
  {
    list($seqs1, $labels1) = $info1->import();
    list($seqs2, $labels2) = $info2->import();
    $info1->link_sequences($info2);
    
    echo "DNA FILE:\n\n";
    $this->__write_report($seqs1, $labels1);
    echo "\nPROTEIN FILE:\n\n";
    $this->__write_report($seqs2, $labels2);
  }
  
  public function import_sequence_file($file)
  {
    $file = $this->__get_file($file);
    if(!file_exists($file)) {
      echo "File $file doesn't exist\n";
      return;
    }
    
    $this->load->plugin('import_info');
    ImportInfo::set_fast();
    $this->load->library('SequenceImporter');
    
    $info = $this->sequenceimporter->import_file($file);
    if(!$info) {
      echo "Invalid sequence file: $file\n";
      return;
    }
    
    $ret = $info->import();

    if(is_numeric($ret)) {
      echo "Imported $ret sequences\n";
    } else { 
      list($seqs, $labels) = $ret;
      $this->__write_report($seqs, $labels);
    }
  }
  
  private function __write_report(&$seqs, &$labels)
  {
    if($labels && count($labels) > 0) {
      echo "Label report:\n";
      foreach ($labels as $name => &$label) {
        echo "-> $name " . $label['type'] . " " . $label['status'] . "\n";
      }
    } else {
      echo "No labels present\n";
    }
    
    if($seqs && count($seqs) > 0) {
      echo "Sequence report:\n";
      foreach ($seqs as $name => &$seq) {
        $content = $seq['short_content'];
        $new = ($seq['add'] ? "New" : "Updated");
        $comment = $seq['comment'];
        
        echo "-> $new $name $content... $comment\n";
        
        foreach ($seq['labels'] as $name => &$label) {
          $status = $label['status'];
          echo "\t-> $name $status\n";
        }
      }
    } else {
      echo "No sequences present\n";
    }
  }
  
  public function add_url_label($url, $param)
  {
    $url = $this->__get_file($url);
    $seqs = $this->sequence_model->get_all();
    $label_id = $this->label_model->get_id_by_name('url');
    echo $url . " -> " . $param . "\n";
    
    $data = new LabelData($url, $param);
    foreach($seqs as &$seq) {
      $seq_id = $seq['id'];
      
      $this->label_sequence_model->add_url_label($seq_id, $label_id, $data);
    }
  }
  
  public function add_refpos_label($start, $length)
  {
    $seqs = $this->sequence_model->get_all();
    $label_id = $this->label_model->get_id_by_name('refpos');
    
    foreach($seqs as &$seq) {
      $seq_id = $seq['id'];
      
      $this->label_sequence_model->add_position_label($seq_id, $label_id, $start, $length);
    }
  }
  
  public function edit_refpos_label($start, $length)
  {
    $seqs = $this->sequence_model->get_all();
    $label_id = $this->label_model->get_id_by_name('refpos');
    
    foreach($seqs as &$seq) {
      $seq_id = $seq['id'];
      
      $id = $this->label_sequence_model->get_label_id($seq_id, $label_id);
      
      $this->label_sequence_model->edit_position_label($id, $start, $length);
    }
  }
  
  public function import_labels($file)
  {
    $file = $this->__get_file($file);
    if(!file_exists($file)) {
      echo "File $file doesn't exist\n";
      return;
    }
    
    $this->load->library('LabelImporter');
    
    $labels = $this->labelimporter->import_xml($file);
    
    if($labels) {
      echo "Label report:\n\n";
      foreach ($labels as &$label) {
        $success = ($label['ret'] ? "Success" : "Not Successful");
        $name = $label['name'];
        $mode = $label['mode'];
        $type = $label['type'];
        echo "-> $name $type $mode $success\n";
      }
    } else {
      echo "Error reading XML file: $file\n";
      return;
    }
  }
  
  public function import_ranks($file)
  {
    $file = $this->__get_file($file);
    if(!file_exists($file)) {
      echo "File $file doesn't exist\n";
      return;
    }
    
    $this->load->library('RankImporter');
    
    $ranks = $this->rankimporter->import_xml($file);
    if ($ranks) {
      echo "Ranks report:\n";
      
      foreach ($ranks as &$rank) {
        $name = $rank['rank_name'];
        $parent = $rank['rank_parent_name'];
        $mode = $rank['mode'];
        $success = $rank['id'] > 0 ? "Successful" : "Not Successful";
        
        echo "-> $name $parent $mode $success\n";
      }
    } else {
      echo "Error reading XML file: $file\n";
      return;
    }
  }
  
  public function import_tree($file)
  {
    $file = $this->__get_file($file);
    if(!file_exists($file)) {
      echo "File $file doesn't exist\n";
      return;
    }
    
    $this->load->library('TreeImporter');
    
    $stats = $this->treeimporter->import_xml($file);
    
    if($stats) {
      $name = $stats['name'];
      $mode = $stats['mode'];
      $new_ranks = $stats['new_ranks'];
      $new_tax = $stats['new_tax'];
      $old_tax = $stats['old_tax'];
      
      if($mode == 'edit') {
        echo "Tree $name was edited\n";
      } else {
        echo "Tree $name was created\n";
      }
      
      echo "$new_ranks ranks were added\n";
      echo "$new_tax taxonomies were created\n";
      echo "$old_tax taxonomies were already present\n";
    } else {
      echo "Error reading XML file: $file\n";
      return;
    }
  }
}
