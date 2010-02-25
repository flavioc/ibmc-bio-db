<?php

class Blast extends BioController
{
  private $advanced_options = '';
  private $blast_program = null;
  private $db_genetic_code = null;
  private $expect_value = null;
  private $identifier = '';
  private $matrix = null;
  private $query_genetic_code = null;
  private $query_sequences = '';
  private $ungapped_alignment = null;
  private $mask_lookup = null;
  private $low_complexity = null;
  
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
  
  private function view_blast_form()
  {
    $search = $this->__get_search_term('post', 'encoded_tree', $encoded, $raw);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    
    $data = $this->search_model->get_search($search,
      array('start' => 0,
            'size' => 1,
            'transform' => $transform,
            'select' => 'id'));
    
    if(!$data) {
      $this->smarty->view('blast/no_data');
      return;
    }
    
    $this->load->model('sequence_model');
    $type = $this->sequence_model->get_type($data[0]['id']);
    
    $this->use_mygrid();
    $this->use_plusminus();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    
    $this->smarty->assign('tree_json', $raw);
    $this->smarty->assign('encoded', addmyslashes($encoded));
    $this->smarty->assign('encoded_no_slashes', $encoded);
    $this->smarty->assign('transform', $transform);
    
    $this->load->library('BlastLib');
    
    if($this->identifier)
      $this->smarty->set_initial_var('identifier', $this->identifier);
    if($this->query_sequences)
      $this->smarty->set_initial_var('query_sequences', $this->query_sequences);
    
    $programs = null;
    if($type == 'dna')
      $programs = array('blastn');
    else
      $programs = array('blastp', 'tblastx', 'blastp', 'tblastn');
    
    $this->smarty->assign('blast_programs', build_data_array($programs, 'id'));
    if($this->blast_program)
      $this->smarty->set_initial_var('blast_program', $this->blast_program);
    
    $this->smarty->assign('expect_values', BlastLib::$expect_values);
    if($this->expect_value)
      $this->smarty->set_initial_var('expect_value', $this->expect_value);
                                                 
    $this->smarty->assign('matrix', BlastLib::$matrix_values);
    if($this->matrix)
      $this->smarty->set_initial_var('matrix', $this->matrix);
      
    if($this->low_complexity)
      $this->smarty->set_initial_var('low_complexity', $this->low_complexity);
      
    if($this->mask_lookup)
      $this->smarty->set_initial_var('mask_lookup', $this->mask_lookup);
      
    if($this->ungapped_alignment)
      $this->smarty->set_initial_var('ungapped_alignment', $this->ungapped_alignment);
    
    $this->smarty->assign('query_genetic_codes', array(array('id' => 1, 'name' => 'Standard (1)'),
                                                 array('id' => 2, 'name' => 'Vertebrate Mitochondrial (2)'),
                                                 array('id' => 3, 'name' => 'Yeast Mitochondrial (3)'),
                                                 array('id' => 4, 'name' => 'Mold, Protozoan, and Coelocoel Mitochondrial (4)'),
                                                 array('id' => 5, 'name' => 'Invertebrate Mitochondrial (5)'),
                                                 array('id' => 6, 'name' => 'Ciliate Nuclear (6)'),
                                                 array('id' => 9, 'name' => 'Echinoderm Mitochondrial (9)'),
                                                 array('id' => 10, 'name' => 'Euplotid Nuclear (10)'),
                                                 array('id' => 11, 'name' => 'Bacterial (11)'),
                                                 array('id' => 12, 'name' => 'Alternative Yeast Nuclear (12)'),
                                                 array('id' => 13, 'name' => 'Ascidian Mitochondrial (13)'),
                                                 array('id' => 14, 'name' => 'Flatworm Mitochondrial (14)'),
                                                 array('id' => 15, 'name' => 'Blepharisma Macronuclear (15)')));
    if($this->query_genetic_code)
      $this->smarty->set_initial_var('query_genetic_code', $this->query_genetic_code);
    
    $this->smarty->assign('db_genetic_codes', array(array('id' => 1, 'name' => 'Standard (1)'),
                                                    array('id' => 2, 'name' => 'Vertebrate Mitochondrial (2)'),
                                                    array('id' => 3, 'name' => 'Yeast Mitochondrial (3)'),
                                                    array('id' => 4, 'name' => 'Mold, Protozoan, and Coelocoel Mitochondrial (4)'),
                                                    array('id' => 5, 'name' => 'Invertebrate Mitochondrial (5)'),
                                                    array('id' => 6, 'name' => 'Ciliate Nuclear (6)'),
                                                    array('id' => 9, 'name' => 'Echinoderm Mitochondrial (9)'),
                                                    array('id' => 10, 'name' => 'Euplotid Nuclear (10)'),
                                                    array('id' => 11, 'name' => 'Bacterial (11)'),
                                                    array('id' => 12, 'name' => 'Alternative Yeast Nuclear (12)'),
                                                    array('id' => 13, 'name' => 'Ascidian Mitochondrial (13)'),
                                                    array('id' => 14, 'name' => 'Flatworm Mitochondrial (14)'),
                                                    array('id' => 15, 'name' => 'Blepharisma Macronuclear (15)')));
    if($this->db_genetic_code)
      $this->smarty->set_initial_var('db_genetic_code', $this->db_genetic_code);
                                                    
    if($this->advanced_options)
      $this->smarty->set_initial_var('advanced_options', $this->advanced_options);
    
    $this->smarty->assign('title', 'BLAST search');
    $this->smarty->view('search/blast');
  }
  
  public function blast_form()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
      
    $this->view_blast_form();
  }
  
  private function __valid_query_sequences()
  {
    return $this->query_sequences != '';
  }
  
  public function do_blast()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
    
    $this->advanced_options = $this->get_post('advanced_options');
    $this->blast_program = $this->get_post('blast_program');
    $this->db_genetic_code = $this->get_post('db_genetic_code');
    $this->expect_value = $this->get_post('expect_value');
    $this->identifier = $this->get_post('identifier');
    $this->matrix = $this->get_post('matrix');
    $this->query_genetic_code = $this->get_post('query_genetic_code');
    $this->query_sequences = $this->get_post('query_sequences');
    $this->ungapped_alignment = $this->get_post('ungapped_alignment');
    $this->mask_lookup = $this->get_post('mask_lookup');
    $this->low_complexity = $this->get_post('low_complexity');
    
    if(!$this->identifier || !$this->__valid_query_sequences()) {
      $this->view_blast_form();
      return;
    }
    
    $this->use_mygrid();
    $this->use_plusminus();
    $search = $this->__get_search_term('post', 'encoded_tree', $encoded);
    $transform = $this->__get_transform_label('transform_hidden', 'post');
    $seqs = $this->search_model->get_search($search,
      array('transform' => $transform,
            'only_public' => !$this->logged_in,
            'select' => 'id, content, name'));
    
    $type_db = $this->__type_db($seqs);
    
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
    $query_file = $this->sequenceexporter->write_fasta_to_file($this->query_sequences);
    
    // run blast
    
    $blast = find_executable('blastall');
    $output_file = generate_new_file_name();
    $cmd = "$blast -p " . $this->blast_program . " -i $query_file -d $file -o $output_file";
    
    // expect value
    $cmd .= " -e " . $this->blastlib->lookup_expect_value($this->expect_value);
    
    // perform ungapped
      
    // score matrix
    $cmd .= " -M " . $this->blastlib->lookup_matrix_value($this->matrix);
      
    // query genetic code
    if($this->blast_program == 'blastx')
      $cmd .= " -Q " . $this->query_genetic_code;
      
    // db genetic code
    if($this->blast_program == 'tblastn' || $this->blast_program == 'tblastx')
      $cmd .= " -D " . $this->db_genetic_code;
      
    // filtering
    if($this->mask_lookup || $this->low_complexity) {
      $cmd .= " -F \"";
      
      if($this->mask_lookup)
        $cmd .= "m ";
      if($this->low_complexity)
        $cmd .= "L ";
      $cmd .= "\"";
    }
    
    // format output
    $cmd .= " -m 3";
    
    // advanced options
    if($this->advanced_options)
      $cmd .= " " . $this->advanced_options;
      
    $out = shell_exec($cmd);
    if($out) {
      die("Failed to run blastall");
    }
    
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
    
    // get query sequence names
    preg_match_all('/Query= (.*)\n/U', $output, $matches);
    $queries = array();
    foreach($matches[1] as &$match)
      $queries[] = $match;
  
    // get blast matches
    preg_match_all('/Sequences producing significant alignments:.*\n\n(.*)\n\n/msU', $output, $matches);
  
    $i = 0;
    $data = array();
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
        if($this->identifier)
          $param .= $this->identifier . ":";
        $param .= $query;
        $data[] = array('id' => $id, 'param' => $param, 'evalue' => $e_value, 'score' => $score);
      }
      $i++;
    }
    $json = json_encode($data);
    
    $this->smarty->assign('labels', $json);
    $this->smarty->assign('title', 'BLAST results');
    $this->smarty->view('blast/results');
  }
  
  public function add_labels()
  {
    if(!$this->logged_in)
      return $this->invalid_permission();
      
    $labels = $this->get_post('labels');
    $data = json_decode($labels, true);
    
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
    $this->load->library('SeqSearchTree');
    
    $evalue_id = $this->label_model->get_id_by_name('evalue');
    $score_id = $this->label_model->get_id_by_name('blast_score');
    $query_id = $this->label_model->get_id_by_name('blast_query');
    
    $ids = array();
    
    foreach($data as &$el) {
      $id = $el['id'];
      $param = $el['param'];
      
      if($evalue_id) {
        $evalue = $el['evalue'];
        $this->label_sequence_model->add_float_label($id, $evalue_id, new LabelData($evalue, $param));
      }
      
      if($score_id) {
        $score = $el['score'];
        $this->label_sequence_model->add_float_label($id, $score_id, new LabelData($score, $param));
      }
      
      if($query_id) {
        $this->label_sequence_model->add_bool_label($id, $query_id, new LabelData(true, $param));
      }
      
      $ids[] = $id;
    }
    
    $tree = $this->seqsearchtree->get_tree_ids($ids);
    
    $this->use_mygrid();
    $this->smarty->assign('encoded', addmyslashes(json_encode($tree)));
    $this->smarty->assign('title', 'BLAST results');
    $this->smarty->view('blast/labels');
  }
}