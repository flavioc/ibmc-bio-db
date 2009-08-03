<?php

class Sequence_model extends BioModel
{
  function Sequence_model() {
    parent::BioModel('sequence');
  }

  function __select()
  {
    $this->db->select('id, content, name, update_user_id, `update`, user_name');
  }

  function get_by_name($name)
  {
    return $this->get_row('name', $name);
  }

  function __filter($filtering)
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
  }

  function get_all($start = null, $size = null,
    $filtering = array(),
    $ordering = array())
  {
    $this->order_by($ordering, 'name', 'asc');
    $this->__filter($filtering);

    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    $this->db->select('id, name, update_user_id, `update`, user_name');

    return parent::get_all('sequence_info_history');
  }

  function get_total($filtering = array())
  {
    $this->__filter($filtering);
    return $this->count_total('sequence_info_history');
  }

  function add($name, $content)
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

  function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'sequence_info_history');
  }

  function delete($id)
  {
    $this->delete_id($id);
  }

  function has_sequence($id)
  {
    return $this->has_id($id);
  }

  function get_content($id)
  {
    return $this->get_field($id, 'content');
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function has_name($name)
  {
    return $this->has_field('name', $name);
  }
  
  function has_same_sequence($name, $content)
  {
    $this->db->where('name', $name);
    $this->db->where('content', $content);
    return $this->has_something();
  }

  function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }

  function edit_name($id, $name)
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

  function permission_public($id)
  {
    $label_sequence = $this->load_model('label_sequence_model');
    $data =  $label_sequence->get_label($id, 'perm_public');

    if($data == null) {
      return false;
    }

    return $data;
  }

  function edit_content($id, $content)
  {
    $content = sequence_normalize($content);
    
    if(strlen($content) <= 0 || strlen($content) > 65535) {
      return false;
    }
    
    $this->db->trans_start();

    $this->update_history($id);
    $ret = $this->edit_field($id, 'content', $content);
    if($ret) {
      $label_sequence = $this->load_model('label_sequence_model');
      $ret = $label_sequence->regenerate_labels($id);
    }

    $this->db->trans_complete();
    
    return $ret;
  }
}
