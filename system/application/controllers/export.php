<?php

class Export extends BioController
{
  function Export()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_sequence_model');
  }
  
  public function get_export()
  {
    $this->load->library('SequenceExporter');
    
    $tree = $this->__get_search_term('post', 'tree', $tree_str, $raw);
    $transform = $this->__get_transform_label('transform', 'post');
    
    $this->load->model('search_model');
    $seqs = $this->search_model->get_search($tree,
      array('transform' => $transform, 'only_public' => !$this->logged_in));
    
    $type = $this->get_post('format');

    if($type == 'fasta' || $type == 'xml') {
      $labels_str = $this->get_post('label_obj');
      $labels = json_decode(stripslashes($labels_str), true);

      return $this->__export_sequences_partial($seqs, $labels, array($tree, $transform), $type);
    } else {
      return $this->__export_sequences($seqs, $type, '');
    }
  }
  
  public function export_search()
  {
    $this->smarty->assign('title', 'Export search');
    $this->smarty->load_stylesheets('export.css', 'operations.css');

    $tree = $this->__get_search_term('post', 'encoded_tree', $encoded, $raw);
    $this->smarty->assign('tree_json', $raw);
    $tree_str = search_tree_to_string($tree);
    $this->smarty->assign('tree_str', $tree_str);

    $transform = $this->__get_transform_label('transform_hidden', 'post');
    
    $this->smarty->assign('transform', $transform);
    
    $this->load->model('search_model');
    $seqs = $this->search_model->get_search($tree,
      array('transform' => $transform, 'only_public' => !$this->logged_in));
    
    $all = $this->label_sequence_model->get_all_labels($seqs);
    $this->smarty->assign('labels', $all);

    $this->smarty->view('export/search');
  }
  
  private function __can_access($id)
  {
    return $this->logged_in || $this->sequence_model->permission_public($id);
  }
  
  public function export_one()
  {
    $id = $this->get_post('id');
    
    if(!$this->__can_access($id)) {
      return $this->invalid_permission();
    }

    if(!$this->sequence_model->has_sequence($id)) {
      return;
    }
    
    $type = $this->get_post('format');
    $seqs = array($this->sequence_model->get($id));
    
    $this->load->library('SequenceExporter');

    return $this->__export_sequences($seqs, $type, "sequence id $id");
  }
  
  public function export_all()
  {
    $this->load->library('SequenceExporter');
    
    $filter_name = $this->get_post('export_name');
    $filter_user = $this->get_post('export_user');

    $type = $this->get_post('format');
    
    if(!$filter_user && !$filter_name) {
      $comment = "all sequences";
    } else {
      $comment = '';
      if($filter_name) {
        $comment = "filter name $filter_name";
      }
      
      if($filter_user) {
        if($comment) {
          $comment .= ' ';
        }
        $this->load->model('user_model');
        $user = $this->user_model->get_name($filter_user);
        $comment .= "by user $user";
      }
    }
    
    return $this->__export_sequences(
      $this->sequence_model->get_all(null, null,
                    array('name' => $filter_name,
                          'user' => $filter_user,
                          'only_public' => !$this->logged_in)),
                $type, $comment);
  }
  
  private function __export_sequences_partial($sequences, $labels_id, $data, $type)
  {
    $seq_labels = array();

    foreach($sequences as &$seq)
    {
      // get sequence content
      $seq = $this->sequence_model->get($seq['id']);

      // try to get the label ids for this sequence
      $labels = array();
      foreach($labels_id as $label_id) {
        $new = $this->label_sequence_model->get_label_ids($seq['id'], $label_id);
        if($new != null) {
          $labels[] = $new;
        }
      }
      $seq_labels[] = $labels;
    }

    list($tree, $transform) = $data;
    $tree_str = search_tree_to_string($tree);
    
    if($transform) {
      $this->load->model('label_model');
      $trans_name = $this->label_model->get_name($transform);
    } else {
      $trans_name = null;
    }
    
    if($type == 'fasta') {
      $comment = " - $tree_str";
      if($trans_name) {
        $comment = "$comment - transformed by label $trans_name";
      }
      $this->__do_export_fasta($sequences, $seq_labels, $comment);
    } else {
      $comment = $tree_str;
      if($trans_name) {
        $comment = "$comment ; transformed by label $trans_name";
      }
      $this->__do_export_xml($sequences, $seq_labels, $comment);
    }
  }

  private function __export_sequences($sequences, $type, $comment)
  {
    foreach($sequences as &$seq) {
      $id = $seq['id'];
      if(!array_key_exists('content', $seq)) {
        $seq['content'] = $this->sequence_model->get_content($id);
      }
    }
    
    if($type == 'fasta' || $type == 'xml') {
      $seq_labels = $this->label_sequence_model->get_sequences($sequences);

      if($type == 'fasta') {
        $this->__do_export_fasta($sequences, $seq_labels, "- $comment");
      } else {
        $this->__do_export_xml($sequences, $seq_labels, $comment);
      }
    } else {
      $this->__do_export_others($sequences, $type);
    }
  }

  private function __do_export_others($sequences, $type)
  {
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"sequences.$type\"");
    
    echo $this->sequenceexporter->export_others($sequences, $type);
  }

  private function __do_export_fasta($sequences, $seq_labels, $extra_comments = '')
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.fasta"');
    
    echo $this->sequenceexporter->export_fasta($sequences, $seq_labels,
      $this->__get_basic_comments() . " $extra_comments");
  }

  private function __do_export_xml($sequences, $seq_labels, $comment)
  {
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="sequences.xml"');
    
    echo $this->sequenceexporter->export_xml($sequences, $seq_labels, $this->username, $comment);
  }

  private function __get_basic_comments()
  {
    return $this->username . ' - ' . timestamp_string();
  }
}