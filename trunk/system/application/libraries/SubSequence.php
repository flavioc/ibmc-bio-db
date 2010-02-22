<?php

class SubSequence
{
  private $search_model = null;
  private $label_sequence_model = null;
  private $label_model = null;
  private $sequence_model = null;
  
  private $failed = array();
  private $result = array();
  
  function SubSequence()
  {
    $this->search_model = load_ci_model('search_model');
    $this->label_sequence_model = load_ci_model('label_sequence_model');
    $this->label_model = load_ci_model('label_model');
    $this->sequence_model = load_ci_model('sequence_model');
  }
  
  public function generate($search, $transform, $only_public, $label_position, $keep = false)
  {
    $label = $this->label_model->get($label_position);
    if($label['type'] != 'position') {
      return null;
    }
    
    $super_id = $this->label_model->get_id_by_name('super');
    $super_position_id = $this->label_model->get_id_by_name('super_position');
    $immutable_id = $this->label_model->get_id_by_name('immutable');
    $subsequence_id = $this->label_model->get_id_by_name('subsequence');
    $lifetime_id = $this->label_model->get_id_by_name('lifetime');
    
    $result = $this->search_model->get_search($search,
      array('transform' => $transform,
            'only_public' => $only_public,
            'select' => 'id, name'));
    
    $failed = array();  
    $sequences = array();
    
    foreach($result as &$row) {
      $id = $row['id'];
      $name = $row['name'];
      
      $label_instances = $this->label_sequence_model->get_label_infos($id, $label_position);
      $seq_length = $this->sequence_model->get_content_length($id);
      
      foreach($label_instances as &$label_instance) {
        $data = label_get_type_data($label_instance, 'position');
        
        $param = $label_instance['param'];
        $start = (int)$data[POSITION_START_INDEX];
        $length = (int)$data[POSITION_LENGTH_INDEX];
        
        $end = $start + $length - 1;
        
        if($end <= $seq_length) {
          $sub_name = build_sub_sequence_name($name, $start, $length);
          $sub_content = $this->sequence_model->get_content_segment($id, $start, $length);
          
          $new_id = $new_id = $this->sequence_model->add($sub_name, $sub_content);
          
          $this->label_sequence_model->add_ref_label($new_id, $super_id, $id);
          $this->label_sequence_model->add_position_label($new_id, $super_position_id, $start, $length);
          $this->label_sequence_model->add_bool_label($new_id, $immutable_id, true);
          $this->label_sequence_model->add_ref_label($id, $subsequence_id,
            new LabelData($new_id, "$start,$length"));
          
          if($keep) {
            $this->label_sequence_model->remove_labels_sequence($lifetime_id, $new_id);
          } else {
            $plus = time() + 60 * 60 * 24 * 3; // 3 days
            $date = date("d-m-Y H:i:s", $plus);
          
            $this->label_sequence_model->add_date_label($new_id, $lifetime_id, $date);
          }
            
          $sequences[] = $new_id;
        } else {
          $failed[] = array('id' => $id, 'name' => $name, 'position' => "$start [$length]");
        }
      }
    }
    
    if(count($sequences) == 0) {
      return false;
    }
    
    $CI =& get_instance();
    $CI->load->library('SeqSearchTree');
    
    $this->failed = $failed;
    $this->result = $CI->seqsearchtree->get_tree_ids($sequences);
    
    return true;
  }
  
  public function get_search_tree()
  {
    return $this->result;
  }
  
  public function get_failed()
  {
    return $this->failed;
  }
}
