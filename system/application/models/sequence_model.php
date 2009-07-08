<?php

class Sequence_model extends BioModel
{
  function Sequence_model() {
    parent::BioModel('sequence');
  }

  function __select()
  {
    $this->db->select('id, content, name, update_user_id, update, user_name');
  }

  function get_by_name($name)
  {
    return $this->get_row('name', $name);
  }

  function get_ids_array()
  {
    $this->db->select('id');
    $rows = $this->get_all();

    $ret = array();
    foreach($rows as $row) {
      $ret[] = $row['id'];
    }

    return $ret;
  }

  function __filter($filtering)
  {
    if(array_key_exists('name', $filtering)) {
      $name = $filtering['name'];
      if(!sql_is_nothing($name)) {
        $this->db->like('name', "%$name%");
      }
    }

    if(array_key_exists('user', $filtering)) {
      $user = $filtering['user'];
      if(!sql_is_nothing($user)) {
        $this->db->where('update_user_id', $user);
      }
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

    $this->db->select('id, name, update_user_id, update, user_name');

    return parent::get_all('sequence_info_history');
  }

  function get_total($filtering = array())
  {
    $this->__filter($filtering);
    return $this->count_total('sequence_info_history');
  }

  function add($name, $content)
  {
    $data = array(
      'name' => $name,
      'content' => sequence_normalize($content),
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

  function get_id_by_name($name)
  {
    return $this->get_id_by_field('name', $name);
  }

  function edit_name($id, $name)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
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
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'content', $content);
    $label_sequence = $this->load_model('label_sequence_model');
    $label_sequence->regenerate_labels($id);

    $this->db->trans_complete();
  }
}
