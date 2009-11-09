<?php

define('POSITION_START_INDEX', 0);
define('POSITION_LENGTH_INDEX', 1);

class LabelData
{
  private $data = null;
  private $param = null;
  
  function LabelData($data, $param = null)
  {
    $this->data = $data;
    $this->param = $param;
  }
  
  public function get_data()
  {
    return $this->data;
  }
  
  public function set_data($data)
  {
    $this->data = $data;
  }
  
  public function has_param()
  {
    return $this->param != null;
  }
  
  public function get_param()
  {
    return $this->param;
  }
  
  public function set_param($val)
  {
    $this->param = $val;
  }
};

function label_get_data($obj)
{
  if($obj instanceof LabelData)
    return $obj->get_data();
  else
    return $obj;
}

function label_get_param($obj)
{
  if($obj instanceof LabelData)
    return $obj->get_param();
  else
    return null;
}

function label_fix_data($type, &$label_data, $label_id, $multiple = false)
{
  $data = label_get_data($label_data);
  
  switch($type) {
    case 'integer':
    case 'float':
    case 'position':
    case 'bool':
    case 'ref':
    case 'tax':
      break;
    case 'obj':
      if($data instanceof FileObject) {
        
        $filename = $data->get_name();
        $len = strlen($filename);
        
        $CI =& get_instance();
        $CI->load->model('file_model');
          
        $id = $CI->file_model->add($data->get_name(), $data->get_content(), $label_id);
        
        if($id)
          $data = $id;
      }
      break;
    case 'text':
    case 'url':
      $data = trim($data);
      break;
    case 'date':
      $data = convert_html_date_to_sql(trim($data));
      break;
  }
  
  if($label_data instanceof LabelData) {
    $label_data->set_data($data);
    
    if(!$multiple) {
      // remove param
      $label_data->set_param(null);
    }
  } else {
    $label_data = $data;
  }
}

function label_validate_data($type, $label_data)
{
  $data = label_get_data($label_data);
  
  switch($type) {
    case 'integer':
      return isint($data);
    case 'float':
      return is_numeric($data);
    case 'url':
      return is_string($data) && strlen($data) <= 2048 && (parse_url($data) ? TRUE : FALSE);
    case 'text':
      if(!is_string($data)) {
        return false;
      }
      $len = strlen($data);
      return $len > 0 && $len <= 1024;
    case 'bool':
      return true;
    case 'obj':
      if(!isint($data)) {
        return false;
      }
        
      $CI =& get_instance();
      $CI->load->model('file_model');
      
      $has_id = $CI->file_model->has_id($data);
      return $has_id;
    case 'position':
      if(!is_array($data) || count($data) != 2) {
        return false;
      }
      
      $start = $data[0];
      $length = $data[1];
      
      if(!is_numeric($start) || !is_numeric($length)) {
        return false;
      }
      
      $start = intval($start);
      $length = intval($length);
      
      return $start >= 0 && $length > 0;
    case 'ref':
      $CI =& get_instance();
      $CI->load->model('sequence_model');
      $seq_model = $CI->sequence_model;
      
      if(!is_numeric($data)) {
        return false;
      }
      $num = intval($data);
      return $num > 0 &&
        $seq_model->has_sequence($num);
    case 'tax':
      if(!is_numeric($data)) {
        return false;
      }
      $num = intval($data);
      $CI =& get_instance();
      $CI->load->model('taxonomy_model');
      $tax_model = $CI->taxonomy_model;
      return $num > 0 &&
        $tax_model->has_taxonomy($num);
    case 'date':
      return $data != null;
  }
  
  return false;
}

function label_data_fields($type)
{
  switch($type) {
  case 'integer':
    return 'int_data';
  case 'text':
  case 'float':
  case 'bool':
  case 'date':
  case 'url':
  case 'ref':
  case 'obj':
    return $type . '_data';
  case 'position':
    return array('position_start', 'position_length');
  case 'tax':
    return 'taxonomy_data';
  }

  return null;
}

function label_get_type_data(&$label_info, $type = null)
{
  if($type == null) {
    $type = $label_info['type'];
  }
  
  $fields = label_data_fields($type);
  
  if(is_array($fields)) {
    $field1 = $fields[0];
    $field2 = $fields[1];
    
    return array($label_info[$field1], $label_info[$field2]);
  } else {
    return $label_info[$fields];
  }
}

function label_transform_data_array_ordered($array)
{
  $ret = array();
  $i = 0;
  
  foreach($array as &$data) {
    ++$i;
    
    $ret[] = new LabelData($data, $i);
  }
  
  return $ret;
}

function label_special_purpose($name)
{
  return in_array($name, array('name', 'content', 'creation_user', 'update_user', 'creation_date', 'update_date'));
}

function label_special_operator($oper)
{
  return $oper == 'exists' || $oper == 'notexists';
}

function label_compound_oper($oper)
{
  return $oper == 'or' || $oper == 'and' || $oper == 'not';
}

function label_valid_type($type)
{
  switch($type) {
    case 'bool':
    case 'integer':
    case 'float':
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

function label_get_printable_string($row, $type = null)
{
  if($type == null) {
    $type = $row['type'];
  }
  
  $data = label_get_type_data($row);
  $ret = null;
  
  switch($type) {
    case 'position':
      $ret = $data[0] . ' ' . $data[1];
      break;
    case 'bool':
      $ret = parse_yes_r_full($data);
      break;
    case 'tax':
      $ret = $row['taxonomy_name'];
      break;
    case 'ref':
      $ret = $row['sequence_name'];
      break;
    case 'obj':
      $ret = $row['obj_data'];
      break;
    default:
      $ret = $data;
      break;
  }
  
  return $ret;
}

function label_type_is_printable($type)
{
  switch($type) {
  case 'integer':
  case 'float':
  case 'text':
  case 'tax':
  case 'url':
  case 'bool':
  case 'ref':
  case 'position':
  case 'date':
  case 'obj':
    return true;
  default:
    return false;
  }
}