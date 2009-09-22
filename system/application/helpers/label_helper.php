<?php

define('POSITION_START_INDEX', 0);
define('POSITION_LENGTH_INDEX', 1);
define('OBJ_FILE_NAME_INDEX', 0);
define('OBJ_DATA_INDEX', 1);

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
};

function label_get_data($obj)
{
  if($obj instanceof LabelData) {
    return $obj->get_data();
  } else {
    return $obj;
  }
}

function label_get_param($obj)
{
  if($obj instanceof LabelData) {
    return $obj->get_param();
  } else {
    return null;
  }
}

function label_fix_data($type, &$label_data)
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
    case 'text':
    case 'url':
    case 'obj':
      $data = trim($data);
      break;
    case 'date':
      $data = convert_html_date_to_sql(trim($data));
      break;
  }
  
  if($label_data instanceof LabelData) {
    $label_data->set_data($data);
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
      if(!is_array($data)) {
        return false;
      }
      
      if(count($data) != 2) {
        return false;
      }
      
      $filename = $data[0];
      
      $len = strlen($filename);
      return $len > 0 && $len <= 1024;
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
      $seq_model = $this->load_model('sequence_model');
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
      $tax_model = $this->load_model('taxonomy_model');
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
    return $type . '_data';
  case 'obj':
    return array('text_data', 'obj_data');
  case 'position':
    return array('position_start', 'position_length');
  case 'tax':
    return 'taxonomy_data';
  }

  return null;
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