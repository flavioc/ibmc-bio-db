<?php

class Label_model extends BioModel
{
  public static $label_view_fields = 'label_id AS id, type, name, default, public, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, update_user_id, update, user_name, comment';

  function Label_model()
  {
    parent::BioModel('label');
  }

  function has_label($id)
  {
    return $this->has_id($id);
  }

  function __select($totals = false)
  {
    $select = self::$label_view_fields;
    if($totals) {
      $select .= ", label_sequences(label_id) AS num_seqs, total_sequences() as total";
    }
    $this->db->select($select);
  }

  function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'label_info_history', 'label_id');
  }

  function get_by_name($name)
  {
    return $this->get_row('name', $name);
  }

  function get_simple($id, $get_code = false)
  {
    $select = 'id, name, type, must_exist, auto_on_creation,
      auto_on_modification, deletable, editable, multiple';

    if($get_code) {
      $select .= ', code, valid_code';
    }

    $this->db->select($select);
    return $this->get_id($id);
  }

  function __filter_labels($filtering = array())
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if(!sql_is_nothing($name)) {
        $name = $this->db->escape($name);
        $this->db->where("name REGEXP $name");
      }
    }

    if(array_key_exists('type', $filtering)) {
      $type = $filtering['type'];
      if(!sql_is_nothing($type)) {
        $this->db->where('type', $type);
      }
    }

    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
    }

    if(array_key_exists('only_searchable', $filtering)) {
      $searchable = $filtering['only_searchable'];
      if($searchable) {
        $this->db->where("type <> 'obj'");
      }
    }
    
    if(array_key_exists('only_deletable', $filtering)) {
      $deletable = $filtering['only_deletable'];
      if($deletable) {
        $this->db->where('deletable IS TRUE');
      }
    }
  }

  function get_all($start = null, $size = null,
    $filtering = array(), $ordering = array(), $totals = false)
  {
    $this->order_by($ordering, 'name', 'asc');
    $this->__select($totals);
    $this->__filter_labels($filtering);
    $this->limit($start, $size);

    return parent::get_all('label_info_history');
  }

  function get_total($filtering = array())
  {
    $this->db->select('id');
    $this->__filter_labels($filtering);

    return parent::count_total('label_info_history');
  }

  function count_names($name)
  {
    $this->db->where('name', $name);

    return $this->count_total();
  }

  function add($name, $type, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple,
    $default, $public,
    $code,
    $valid_code, $comment)
  {
    $type = trim($type);
    $name = trim($name);
    $comment = trim($comment);
    
    if(strlen($name) <= 0 || strlen($name) > 255) {
      return false;
    }
    
    if($this->has($name)) {
      return false;
    }
    
    if(!$this->__valid_type($type)) {
      return false;
    }
    
    if(strlen($comment) > 1024) {
      return false;
    }
    
    $data = array(
      'name' => $name,
      'type' => $type,
      'must_exist' => $mustexist,
      'auto_on_creation' => $auto_on_creation,
      'auto_on_modification' => $auto_on_modification,
      'deletable' => $deletable,
      'editable' => $editable,
      'multiple' => $multiple,
      'default' => $default,
      'public' => $public,
      'code' => trim($code),
      'valid_code' => trim($valid_code),
      'comment' => $comment,
    );

    return $this->insert_data_with_history($data);
  }

  function edit($id, $name, $type, $mustexist, $auto_on_creation,
    $auto_on_modification, $deletable,
    $editable, $multiple,
    $default, $public,
    $code, $valid_code, $comment)
  {
    $type = trim($type);
    $name = trim($name);
    $comment = trim($comment);
    
    if(strlen($name) <= 0 || strlen($name) > 255) {
      return false;
    }
    
    if($this->has($name)) {
      return false;
    }
    
    if(strlen($comment) > 1024) {
      return false;
    }
    
    if(!$this->__valid_type($type)) {
      return false;
    }
    
    $data = array(
      'name' => $name,
      'type' => $type,
      'must_exist' => $mustexist,
      'auto_on_creation' => $auto_on_creation,
      'auto_on_modification' => $auto_on_modification,
      'deletable' => $deletable,
      'editable' => $editable,
      'multiple' => $multiple,
      'default' => $default,
      'public' => $public,
      'code' => trim($code),
      'valid_code' => trim($valid_code),
      'comment' => $comment,
    );

    return $this->edit_data_with_history($id, $data);
  }

  function has($name)
  {
    return $this->has_field('name', $name);
  }

  function delete_label($id)
  {
    $this->delete_id($id);
    return true;
  }
  
  function delete_all_custom()
  {
    $this->db->where('`default` IS FALSE');
    return $this->delete_rows();
  }

  function is_default($id)
  {
    return $this->get_field($id, 'default');
  }

  function is_deletable($id)
  {
    return $this->get_field($id, 'deletable');
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }
  
  function get_type($id)
  {
    return $this->get_field($id, 'type');
  }
  
  function get_comment($id)
  {
    return $this->get_field($id, 'comment');
  }
  
  function get_code($id)
  {
    return $this->get_field($id, 'code');
  }
  
  function get_validcode($id)
  {
    return $this->get_field($id, 'valid_code');
  }

  function get_to_add()
  {
    $this->db->where('auto_on_creation', TRUE);
    $this->__filter_special_labels();

    return parent::get_all();
  }

  function get_obligatory()
  {
    $this->db->select('id');
    $this->db->where('must_exist', TRUE);
    $this->__filter_special_labels();

    $all = parent::get_all();

    $ret = array();
    foreach($all as $label) {
      $ret[] = intval($label['id']);
    }

    return $ret;
  }

  function edit_name($id, $name)
  {
    $name = trim($name);
    
    if(strlen($name) <= 0 || strlen($name) > 255 || $this->has($name)) {
      return false;
    }

    return $this->edit_field($id, 'name', $name);
  }

  function edit_type($id, $type)
  {
    $type = trim($type);
    
    if(!$this->__valid_type($type)) {
      return false;
    }
    
    return $this->edit_field($id, 'type', $type);
  }

  function edit_code($id, $code)
  {
    return $this->edit_field($id, 'code', trim($code));
  }

  function edit_validcode($id, $code)
  {
    return $this->edit_field($id, 'valid_code', trim($code));
  }

  function edit_comment($id, $comment)
  {
    $comment = trim($comment);
    
    if(strlen($comment) > 1024) {
      return false;
    }
    
    return $this->edit_field($id, 'comment', $comment);
  }

  function edit_bool($id, $what, $val)
  {
    return $this->edit_field($id, $what, $val);
  }
  
  function __valid_type($type)
  {
    switch($type) {
      case 'bool':
      case 'integer':
      case 'obj':
      case 'position':
      case 'ref':
      case 'tax':
      case 'text':
      case 'url':
      case 'date':
        return true;
    }
    
    return false;
  }
}
