<?php

class Blast extends BioController
{
  function Blast()
  {
    parent::BioController();
    $this->load->model('search_model');
    $this->load->model('sequence_model');
  }
  
  private function __type_db(&$seqs)
  {
    $type = null;
    foreach($seqs as &$seq) {
      $id = $seq['id'];
      $new_type = $this->sequence_model->get_type($id);
      if($type && $new_type != $type) {
        return null;
      } else
        $type = $new_type;
    }
    
    return $type;
  }
  
  private function __change_name(&$seqs)
  {
    foreach($seqs as &$seq) {
      $seq['name'] = $seq['id'] . " " . $seq['name'];
    }
  }
  
  public function do_blast()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
    
    $this->use_mygrid();
    $this->use_plusminus();
    $search = $this->__get_search_term('post', 'encoded_tree', $encoded);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $seqs = $this->search_model->get_search($search,
      array('transform' => $transform,
            'only_public' => !$this->logged_in,
            'select' => 'id, content, name'));
      
    $type_db = $this->__type_db($seqs);
      
    $advanced_options = $this->get_post('advanced_options');
    $blast_program = $this->get_post('blast_program');
    $db_genetic_code = $this->get_post('db_genetic_code');
    $expect_value = $this->get_post('expect_value');
    $identifier = $this->get_post('identifier');
    $matrix = $this->get_post('matrix');
    $query_genetic_code = $this->get_post('query_genetic_code');
    $query_sequences = $this->get_post('query_sequences');
    $ungapped_alignment = $this->get_post('ungapped_alignment');
    $mask_lookup = $this->get_post('mask_lookup');
    $low_complexity = $this->get_post('low_complexity');
    $generate_labels = $this->get_post('generate_labels');
    
    $this->load->library('SequenceExporter');
    $this->load->library('BlastLib');
    
    // jump to tmp directory
    $tmpdir = sys_get_temp_dir();
    
    $formatdb = find_executable('formatdb');
    
    $this->__change_name($seqs);
    $file = $this->sequenceexporter->write_sequences_to_fasta($seqs);
    $cmd = $formatdb;
    
    $cmd .= " -i $file -p ";
    switch($type_db) {
      case "dna":
        $cmd .= 'F'; // adn
        break;
      case "protein":
        $cmd .= 'T'; // protein
        break;
    }
    
    shell_exec($cmd);
    // remove fasta file
    unlink($file);
    
    // create query file
    $query_file = $this->sequenceexporter->write_fasta_to_file($query_sequences);
    
    // run blast
    
    $blast = find_executable('blastall');
    $output_file = generate_new_file_name();
    $cmd = "$blast -p $blast_program -i $query_file -d $file -o $output_file";
    
    // expect value
    $cmd .= " -e " . $this->blastlib->lookup_expect_value($expect_value);
    
    // perform ungapped
      
    // score matrix
    $cmd .= " -M " . $this->blastlib->lookup_matrix_value($matrix);
      
    // query genetic code
    if($blast_program == 'blastx')
      $cmd .= " -Q " . $query_genetic_code;
      
    // db genetic code
    if($blast_program == 'tblastn' || $blast_program == 'tblastx')
      $cmd .= " -D " . $db_genetic_code;
      
    // filtering
    if($mask_lookup || $low_complexity) {
      $cmd .= " -F \"";
      
      if($mask_lookup)
        $cmd .= "m ";
      if($low_complexity)
        $cmd .= "L ";
      $cmd .= "\"";
    }
    
    // format output
    $cmd .= " -m 3";
    
    // advanced options
    if($advanced_options)
      $cmd .= " $advanced_options";
      
    $out = shell_exec($cmd);
    $output = read_raw_file($output_file);
    $this->smarty->assign('output', $output);
    
    // remove query file
    unlink($query_file);
    
    // remove output file
    unlink($output_file);
    
    // remove left overs
    $database_files = array($file . '.nhr',
                            $file . '.nin',
                            $file . '.nsq',
                            $file . '.phr',
                            $file . '.pin',
                            $file . '.psq');

    foreach($database_files as $garbage)
      if(file_exists($garbage))
        unlink($garbage);
    
    if($generate_labels) {
      $this->load->model('label_model');
      $this->load->model('label_sequence_model');
      
      // get query sequence names
      preg_match_all('/Query= (.*)\n/U', $output, $matches);
      $queries = array();
      foreach($matches[1] as &$match)
        $queries[] = $match;
    
      // get blast matches
      preg_match_all('/Sequences producing significant alignments:.*\n\n(.*)\n\n/msU', $output, $matches);
    
      $evalue_id = $this->label_model->get_id_by_name('evalue');
      $score_id = $this->label_model->get_id_by_name('blast_score');
      $query_id = $this->label_model->get_id_by_name('blast_query');
    
      $i = 0;
      foreach($matches[1] as &$match) {
        $sequence_parts = explode("\n", $match);
        foreach($sequence_parts as &$part) {
          $comps = preg_split("/\s+/", $part);
          $len = count($comps);
          $e_value = $comps[$len-1];
          $score = $comps[$len-2];
          $id = $comps[0];
          $query = $queries[$i];
          $param = '';
          if($identifier)
            $param .= $identifier . ":";
          $param .= $query;
          $this->label_sequence_model->add_float_label($id, $evalue_id, new LabelData($e_value, $param));
          $this->label_sequence_model->add_float_label($id, $score_id, new LabelData($score, $param));
          $this->label_sequence_model->add_bool_label($id, $query_id, new LabelData(true, $param));
        }
        $i++;
      }
    }
    
    $this->smarty->assign('title', 'BLAST results');
    $this->smarty->view('blast/results');
  }
}