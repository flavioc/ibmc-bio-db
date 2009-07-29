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

  function update_history($id, $table = null)
  {
    $history = $this->load_model('history_model');

    if($this->has_history($id, $table)) {
      return $history->update($this->get_history($id, $table));
    } else {
      return $this->edit_field($id, 'history_id', $history->add(), $table);
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

    if($query->num_rows() < 1) {
      return null;
    }

    return $query->row_array();
  }

  function get_id_by_field($field, $data, $table = null)
  {
    $row = $this->get_row($field, $data, $table);

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
    return $this->delete_by_field('id', $id, $table);
  }

  function delete_by_field($field, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    return $this->db->delete($table, array($field => $data));
  }
  
  function delete_rows($table = null)
  {
    if($table == null) {
      $table = $this->table;
    }
    
    return $this->db->delete($table);
  }
  
  function delete_all($table = null)
  {
    if($table == null) {
      $table = $this->table;
    }
    
    return $this->db->empty_table($table);
  }

  function edit_field($id, $field, $data, $table = null)
  {
    $data = array(
      $field => $data,
    );

    return $this->edit_data($id, $data, $table);
  }

  function edit_data($id, $data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->where('id', $id);
    return $this->db->update($table, $data);
  }

  function edit_data_with_history($id, $data, $table = null)
  {
    $this->db->trans_start();

    $ret = $this->edit_data($id, $data, $table);
    if($ret) {
      $ret = $this->update_history($id, $table);
    }
      
    $this->db->trans_complete();

    return $ret;
  }

  function insert_data_with_history($data, $table = null)
  {
    if($table == null) {
      $table = $this->table;
    }

    $this->db->trans_start();

    $this->db->insert($table, $data);

    $ret = $this->db->insert_id();
    $this->update_history($ret, $table);

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

  function get_order_by($order, $default, $typed)
  {
    foreach($order as $field => $order_type) {
      if($field && $order_type) {
        return array($field, $order_type);
      }
    }

    return array($default, $typed);
  }

  function get_order_sql($order, $default, $typed)
  {
    $arr = $this->get_order_by($order, $default, $typed);
    
    $order = $arr[1];
    if($order != 'asc' || $order != 'desc') {
      return '';
    }
    
    $field = $this->db->protect_identifiers($arr[0]);
    
    return "ORDER BY $field $order";
  }

  function order_by($order, $default, $typed)
  {
    $ret = $this->get_order_by($order, $default, $typed);

    $this->db->order_by($ret[0], $ret[1]);
  }

  function limit($start, $size)
  {
    if($size != null) {
      if(!$start) {
        $start = 0;
      }
      $this->db->limit($size, $start);
    }
  }
  
  function __filter_special_labels()
  {
    foreach(array('name', 'content', 'creation_user', 'update_user', 'creation_date', 'update_date') as $name) {
      $this->db->where("name <> '$name'");
    }
  }
}
