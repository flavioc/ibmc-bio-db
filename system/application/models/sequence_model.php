<?php

class Sequence_model extends BioModel
{
  function Sequence_model()
  {
    parent::BioModel('sequence');
  }

  private function __select()
  {
    $this->db->select('id, content, name, update_user_id, `update`, user_name');
  }

  public function get_by_name($name)
  {
    return $this->get_row('name', $name);
  }

  private function __filter($filtering)
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if(!sql_is_nothing($name)) {
        $name = $this->db->escape($name);
        $this->db->where("name REGEXP $name");
      }
    }

    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
    }
    
    if(array_key_exists('creation_user', $filtering)) {
      $this->db->where('creation_user_id IS NOT NULL');
    }
    
    if(array_key_exists('update_user', $filtering)) {
      $this->db->where('update_user_id IS NOT NULL');
    }
    
    if(array_key_exists('creation_date', $filtering)) {
      $this->db->where('creation IS NOT NULL');
    }
    
    if(array_key_exists('update_date', $filtering)) {
      $this->db->where('`update` IS NOT NULL');
    }
    
    if(array_key_exists('only_public', $filtering)) {
      if($filtering['only_public']) {
        $this->db->where("EXISTS (SELECT * FROM label_sequence_info WHERE label_sequence_info.seq_id = sequence_info_history.id AND label_sequence_info.name = 'perm_public' AND bool_data IS TRUE)", NULL, FALSE);
      }
    }
  }

  public function get_all($start = null, $size = null,
    $filtering = array(),
    $ordering = array(),
    $select = 'id, name, update_user_id, `update`, user_name')
  {
    $this->order_by($ordering, 'name', 'asc');
    $this->__filter($filtering);

    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->select($select);

    return parent::get_all('sequence_info_history');
  }

  public function get_total($filtering = array())
  {
    $this->__filter($filtering);
    return $this->count_total('sequence_info_history');
  }

  public function add($name, $content)
  {
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 255) {
      return false;
    }
    
    $content = sequence_normalize($content);
    if(strlen($content) <= 0 || strlen($content) > 65535) {
      return false;
    }
    
    $data = array(
      'name' => $name,
      'content' => $content,
    );

    $label_sequence = $this->load_model('label_sequence_model');

    $id = $this->insert_data_with_history($data);

    $label_sequence->add_initial_labels($id);

    return $id;
  }

  public function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'sequence_info_history');
  }

  public function delete($id)
  {
    // delete labels
    $labels = $this->load_model('label_sequence_model');
    $labels->remove_labels($id);
    $this->delete_id($id);
  }

  public function has_sequence($id)
  {
    return $this->has_id($id);
  }

  public function get_content($id)
  {
    return $this->get_field($id, 'content');
  }
  
  public function get_creation_user($id)
  {
    return $this->get_field($id, 'creation_user_name', 'sequence_info_history');
  }
  
  public function get_creation_user_id($id)
  {
    return $this->get_field($id, 'creation_user_id', 'sequence_info_history');
  }
  
  public function get_update_user($id)
  {
    return $this->get_field($id, 'user_name', 'sequence_info_history');
  }
  
  public function get_update_user_id($id)
  {
    return $this->get_field($id, 'update_user_id', 'sequence_info_history');
  }
  
  public function get_creation_date($id)
  {
    return $this->get_field($id, 'creation', 'sequence_info_history');
  }
  
  public function get_update_date($id)
  {
    return $this->get_field($id, 'update', 'sequence_info_history');
  }
  
  public function get_content_length($id)
  {
    $this->db->select('CHAR_LENGTH(content) AS length', FALSE);
    
    $row = $this->get_row('id', $id);
    
    if(!$row) {
      return 0;
    }
    
    return (int)$row['length'];
  }
  
  public function get_content_segment($id, $start, $length)
  {
    $this->db->select("SUBSTR(content, $start, $length) AS segment", FALSE);
    
    $row = $this->get_row('id', $id);
    
    if(!$row) {
      return '';
    }
    
    return $row['segment'];
  }
  
  public function get_short_content($id)
  {
    return $this->get_content_segment($id, 1, SEQUENCE_SPACING);
  }

  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  public function has_name($name)
  {
    return $this->has_field('name', $name);
  }
  
  public function has_same_sequence($name, $content)
  {
    $this->db->where('name', $name);
    $this->db->where('content', $content);
    return $this->has_something();
  }

  public function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }
  
  public function get_id_by_name_and_content($name, $content)
  {
    $this->db->where('name', $name);
    return $this->get_id_by_field('content', $content);
  }

  public function edit_name($id, $name)
  {
    $name = trim($name);
    if(strlen($name) <= 0 || strlen($name) > 255) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
    
    return $ret;
  }

  public function permission_public($id)
  {
    $label_sequence = $this->load_model('label_sequence_model');
    $data = $label_sequence->get_label($id, 'perm_public');

    if($data == null) {
      return false;
    }

    return $data;
  }
  
  public function get_type($id)
  {
    return $this->load_model('label_sequence_model')->get_label($id, 'type');
  }
  
  public function is_immutable($id)
  {
    return $this->load_model('label_sequence_model')->get_label($id, 'immutable');
  }
  
  public function get_super($id)
  {
    return $this->load_model('label_sequence_model')->get_label($id, 'super');
  }
  
  public function get_super_position($id)
  {
    return $this->load_model('label_model')->get_label($id, 'super_position');
  }
  
  public function get_lifetime($id)
  {
    return $this->load_model('label_sequence_model')->get_label($id, 'lifetime');
  }
  
  public function get_translated_sequence($id)
  {
    return $this->load_model('label_sequence_model')->get_label($id, 'translated');
  }
  
  public function set_translated_sequence($seq1, $seq2)
  {
    if(!$seq1 || !$seq2) {
      return false;
    }
    
    $type1 = $this->get_type($seq1);
    $type2 = $this->get_type($seq2);
    
    if($type1 == $type2 || !valid_sequence_type($type1) || !valid_sequence_type($type2)) {
      // one must be protein, the other dna
      return false;
    }
    
    $label_id = $this->load_model('label_model')->get_id_by_name('translated');
    $model = $this->load_model('label_sequence_model');
    $model->ensure_translated_label($label_id, $seq1, $seq2);
    $model->ensure_translated_label($label_id, $seq2, $seq1);
    
    return true;
  }

  public function edit_content($id, $content)
  {
    $content = sequence_normalize($content);
    
    if(strlen($content) <= 0 || strlen($content) > 65535) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'content', $content);
    if($ret) {
      $label_sequence_model = $this->load_model('label_sequence_model');
      $ret = $label_sequence_model->regenerate_labels($id);
      
      $label_sequence_model->run_modification_actions($id);
    }

    $this->db->trans_complete();
    
    return $ret;
  }
  
  /* get all sequence ids with the same name */
  public function locate_all($name)
  {
    $this->db->select('id');
    $this->db->where('name', $name);
    $all = parent::get_all();
    
    $ret = array();
    foreach($all as &$seq) {
      $ret[] = $seq['id'];
    }
    
    return $ret;
  }
  
  public function locate_sequence_type($name, $type)
  {
    $ids = $this->locate_all($name);
    
    foreach($ids as $id) {
      $atype = $this->get_type($id);
      if($atype == $type) {
        return $id;
      }
    }
    
    return null;
  }
}
