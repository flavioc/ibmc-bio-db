<?php

class BioModel extends Model
{
  protected $table = '';

  function BioModel($name) {
    parent::Model();
    $this->table = $name;
  }

  function load_model($name)
  {
    $CI =& get_instance();
    $CI->load->model($name, $name, true);
        
    return $CI->$name;
  }

  function has_history($id, $table = null)
  {
    $found = $this->get_field($id, 'history_id', $table);

    return $found != null;
  }

  function new_history()
  {
    $history = $this->load_model('history_model');

    return $history->add();
  }

  function update_history($id, $table = null)
  {
    $history = $this->load_model('history_model');

    if($this->has_history($id, $table)) {
      $history->update($this->get_history($id, $table));
    } else {
      $this->edit_field($id, 'history_id', $history->add(), $table);
    }
  }

  function get_history($id, $table = null)
  {
    return $this->get_field($id, 'history_id', $table);
  }

  function get_field($id, $field, $table = null, $id_field = 'id')
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->select($field);

    $query = $this->db->get_where($table, array($id_field => $id));

    if($query->num_rows() != 1) {
      return null;
    }

    $data = $query->row_array();

    return $data[$field];
  }

  function get_id($id, $table = null, $id_field = 'id')
  {
    return $this->get_row($id_field, $id, $table);
  }

  function get_row($field, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $query = $this->db->get_where($table, array($field => $data));

    if($query->num_rows() != 1) {
      return null;
    }

    return $query->row_array();
  }

  function get_id_by_field($field, $data, $table = null)
  {
    $row = $this->get_row($field, $data, 'id', $table);

    if($row == null) {
      return null;
    }

    return $row['id'];
  }

  function get_rows($field, $data, $select = null, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    if($select != null) {
      $this->db->select($select);
    }

    $query = $this->db->get_where($table, array($field => $data));

    return $query->result_array();
  }

  function get_ids_by_field($field, $data, $table = null)
  {
    return $this->get_rows($field, $data, 'id', $table);
  }

  function has_field($field, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $query = $this->db->get_where($table, array($field => $data));

    return $query->num_rows() == 1;
  }

  function has_id($id, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    return $this->has_field('id', $id, $table);
  }

  function get_all($table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    return $this->db->get($table)->result_array();
  }

  function count_total($table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->from($table);
    return intval($this->db->count_all_results());
  }

  function delete_id($id, $table = null)
  {
    $this->delete_by_field('id', $id, $table);
  }

  function delete_by_field($field, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->delete($table, array($field => $data));
  }

  function edit_field($id, $field, $data, $table = null)
  {
    $data = array(
      $field => $data,
    );

    $this->edit_data($id, $data, $table);
  }

  function edit_data($id, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->where('id', $id);
    $this->db->update($table, $data);
  }

  function edit_data_with_history($id, $data, $table = null)
  {
    $this->db->trans_start();

    $ret = $this->edit_data($id, $data, $table);
    $this->update_history($id, $table);

    $this->db->trans_complete();

    return $ret;
  }

  function insert_data_with_history($data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->trans_start();

    $data['history_id'] = $this->new_history();

    $this->db->insert($table, $data);

    $ret = $this->db->insert_id();

    $this->db->trans_complete();

    return $ret;
  }

  function insert_data($data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->insert($table, $data);

    return $this->db->insert_id();
  }

  function get_all_by_field($field, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $query = $this->db->get_where($table, array($field => $data));

    $data = $query->result_array();

    return $data;
  }

  function copy_data(&$dest, $orig, $fields)
  {
    foreach($fields as $field) {
      if(is_array($field)) {
        $orig_field = $field[0];
        $dest_field = $field[1];
        $dest[$dest_field] = $orig[$orig_field];
      } else {
        $dest[$field] = $orig[$field];
      }
    }
  }

  function expand_history($data, $id = null)
  {
    if($id) {
      $data['history_id' ] = $id;
    } else {
      $id = $data['history_id'];
    }

    $hist_model = $this->load_model('history_model');

    $hist_row = $hist_model->get($id);

    $this->copy_data($data, $hist_row,
      array('creation_user_id', 'creation', 'update_user_id',
        'update', 'user_name', 'complete_name',
        'creation_date', 'password', 'email', 'user_type',
        'birthday', 'image', 'enabled'));

    return $data;
  }

  function rows_sql($sql)
  {
    $query = $this->db->query($sql);

    return $query->result_array();
  }

  function row_sql($sql)
  {
    $query = $this->db->query($sql);

    return $query->row_array();
  }

  function total_sql($sql)
  {
    $data = $this->row_sql($sql);

    return intval($data['total']);
  }
}
