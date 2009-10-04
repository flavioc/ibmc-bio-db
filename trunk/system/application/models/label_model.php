<?php

class Label_model extends BioModel
{
  public static $label_view_fields = 'label_id AS id, type, name, `default`, `public`, must_exist, auto_on_creation, auto_on_modification, `code`, valid_code, deletable, editable, multiple, update_user_id, `update`, user_name, comment, action_modification';

  function Label_model()
  {
    parent::BioModel('label');
  }

  public function has_label($id)
  {
    return $this->has_id($id);
  }

  private function __select($totals = false)
  {
    $select = self::$label_view_fields;
    if($totals) {
      $select .= ", label_sequences(label_id) AS num_seqs, total_sequences() as total";
    }
    $this->db->select($select);
  }

  public function get($id)
  {
    $this->__select();
    return $this->get_id($id, 'label_info_history', 'label_id');
  }

  public function get_by_name($name)
  {
    return $this->get_row('name', $name);
  }
  
  public function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }

  public function get_simple($id, $get_code = false)
  {
    $select = 'id, name, type, must_exist, auto_on_creation,
      auto_on_modification, deletable, editable, multiple';

    if($get_code) {
      $select .= ', code, valid_code';
    }

    $this->db->select($select);
    return $this->get_id($id);
  }

  private function __filter_labels($filtering = array())
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
    
    if(array_key_exists('only_deletable', $filtering)) {
      $deletable = $filtering['only_deletable'];
      if($deletable) {
        $this->db->where('deletable IS TRUE');
      }
    }
    
    if(array_key_exists('only_addable', $filtering)) {
      $this->__filter_special_labels();
    }
    
    if(array_key_exists('only_public', $filtering)) {
      if($filtering['only_public']) {
        $this->db->where('public is TRUE');
      }
    }
  }

  public function get_all($start = null, $size = null,
    $filtering = array(), $ordering = array(), $totals = false)
  {
    $this->order_by($ordering, 'name', 'asc');
    $this->__select($totals);
    $this->__filter_labels($filtering);
    $this->limit($start, $size);

    $ret = parent::get_all('label_info_history');
    
    $sequence_model = $this->load_model('sequence_model');

    if($totals) {
      foreach($ret as &$label) {
        $name = $label['name'];

        if(label_special_purpose($name)) {
          switch($name) {
            case 'name':
            case 'content':
            $label['num_seqs'] = $sequence_model->get_total();
            break;
            case 'creation_user':
            $label['num_seqs'] = $sequence_model->get_total(array('creation_user' => true));
            break;
            case 'update_user':
            $label['num_seqs'] = $sequence_model->get_total(array('update_user' => true));
            break;
            case 'creation_date':
            $label['num_seqs'] = $sequence_model->get_total(array('creation_date' => true));
            break;
            case 'update_date':
            $label['num_seqs'] = $sequence_model->get_total(array('update_date' => true));
            break;
          }
        }
      }
    }
    
    return $ret;
  }

  public function get_total($filtering = array())
  {
    $this->db->select('id');
    $this->__filter_labels($filtering);

    return parent::count_total('label_info_history');
  }

  public function count_names($name)
  {
    $this->db->where('name', $name);

    return $this->count_total();
  }

  public function add($name, $type, $mustexist, $auto_on_creation,
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
    
    $name = str_replace(' ', '_', $name);
    
    if($this->has($name)) {
      return false;
    }
    
    if(!label_valid_type($type)) {
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

  public function edit($id, $name, $type, $mustexist, $auto_on_creation,
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
    
    if(!$this->has($name)) {
      return false;
    }
    
    if(strlen($comment) > 1024) {
      return false;
    }
    
    if(!label_valid_type($type)) {
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

  public function has($name)
  {
    return $this->has_field('name', $name);
  }

  public function delete_label($id)
  {
    $this->delete_id($id);
    
    return true;
  }
  
  public function delete_all_custom()
  {
    $this->db->where('`default` IS FALSE');
    return $this->delete_rows();
  }

  public function is_default($id)
  {
    return $this->get_field($id, 'default');
  }

  public function is_deletable($id)
  {
    return $this->get_field($id, 'deletable');
  }
  
  public function is_multiple($id)
  {
    return $this->get_field($id, 'multiple');
  }

  public function get_name($id)
  {
    return $this->get_field($id, 'name');
  }
  
  public function get_type($id)
  {
    return $this->get_field($id, 'type');
  }
  
  public function get_comment($id)
  {
    return $this->get_field($id, 'comment');
  }
  
  public function get_code($id)
  {
    return $this->get_field($id, 'code');
  }
  
  public function get_validcode($id)
  {
    return $this->get_field($id, 'valid_code');
  }

  public function get_to_add()
  {
    $this->db->where('auto_on_creation', TRUE);
    $this->__filter_special_labels();

    return parent::get_all();
  }

  public function get_obligatory()
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

  public function edit_name($id, $name)
  {
    $name = trim($name);
    
    if(strlen($name) <= 0 || strlen($name) > 255 || $this->has($name)) {
      return false;
    }

    return $this->edit_field($id, 'name', $name);
  }

  public function edit_type($id, $type)
  {
    $type = trim($type);
    
    if(!label_valid_type($type)) {
      return false;
    }
    
    return $this->edit_field($id, 'type', $type);
  }

  public function edit_code($id, $code)
  {
    return $this->edit_field($id, 'code', trim($code));
  }

  public function edit_validcode($id, $code)
  {
    return $this->edit_field($id, 'valid_code', trim($code));
  }

  public function edit_comment($id, $comment)
  {
    $comment = trim($comment);
    
    if(strlen($comment) > 1024) {
      return false;
    }
    
    return $this->edit_field($id, 'comment', $comment);
  }

  public function edit_bool($id, $what, $val)
  {
    return $this->edit_field($id, $what, $val);
  }
  
  public function get_refs()
  {
    return $this->get_all(null, null, array('type' => 'ref'));
  }
  
  public function get_positions()
  {
    return $this->get_all(null, null, array('type' => 'position'));
  }
}
