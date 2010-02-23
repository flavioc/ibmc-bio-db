<?php

function parse_id($str)
{
  $id_vec = explode('_', $str);

  $id = intval($id_vec[count($id_vec)-1]);

  return $id;
}

function parse_yes($yes)
{
  return $yes == 'yes' ? TRUE : FALSE;
}

function parse_yes_r($val)
{
  return $val ? 'Y' : 'N';
}

function parse_yes_r_full($val)
{
  return $val ? 'Yes' : 'No';
}

function newline_tab_html($text)
{
  return ascii_to_entities(str_replace("\t", "   ", str_replace("\n", "<br />", $text)));
}

function xmlspecialchars($text)
{
   return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
}

function xmlspecialchars_decode($text)
{
  return str_replace('&apos;', '&#039;', htmlspecialchars_decode($text, ENT_QUOTES));
}

function find_xml_child($node, $what)
{
  foreach($node->childNodes as $child) {
    if($child->nodeName == $what) {
      return $child;
    }
  }
  
  return null;
}

function parse_boolean_value($val)
{
  if(!$val) {
    return false;
  }
  
  return $val == '1';
}

function change_spaces($str)
{
  return str_replace(' ', '_', $str);
}

function tabs($n)
{
  return str_repeat("\t", $n);
}

function isint($mixed)
{
    return is_int($mixed) || preg_match('/^\d*$/', $mixed) == 1;
}

function build_error_name($what)
{
  return $what . '_error';
}

function build_initial_name($what)
{
  return 'initial_' . $what;
}

function output_autocomplete($words)
{
  foreach($words as $word) {
    echo "$word\n";
  }
}

function output_autocomplete_data($data, $key)
{
  $arr = array();

  foreach($data as $d) {
    $arr[] = $d[$key];
  }

  return output_autocomplete($arr);
}

function build_data_array($array, $column = 'name')
{
  $ret = array();

  foreach($array as $value) {
    $ret[] = array($column => $value);
  }

  return $ret;
}

function convert_html_date_to_sql($date)
{
  $date = trim($date);
  
  $super_vec = explode(' ', $date);
  
  if(count($super_vec) == 0) {
    return null;
  }
  
  $date_block = $super_vec[0];
  $vec = explode('-', $date_block);

  if(count($vec) < 3) {
    return null;
  }
  
  for($i = 0; $i < 3; ++$i) {
    if(!is_numeric($vec[$i])) {
      return null;
    }
  }

  $day = (int)$vec[0];
  $month = (int)$vec[1];
  $year = (int)$vec[2];

  if($year > 1000)
    $ret = "$year-$month-$day";
  else
    $ret = "$day-$month-$year";
  
  if(count($super_vec) == 2) {
    $time_block = $super_vec[1];
    
    $ret .= " $time_block";
  }
  
  return $ret;
}

function convert_sql_date_to_html($date)
{
  $vec = explode('-', $date);

  if(count($vec) != 3) {
    return null;
  }

  $year = $vec[0];
  $month = $vec[1];
  $day = $vec[2];

  return $day . '-' . $month . '-' . $year;
}

function timestamp_string()
{
  return date('l jS F Y h:i:s A');
}

function simple_timestamp_string()
{
  return date('j-n-Y');
}

function read_raw_file($file)
{
  $fp = fopen($file, 'rb');
  $size = filesize($file);

  $content = fread($fp, $size);

  fclose($fp);

  return $content;
}

function read_raw_upload($data)
{
  return read_raw_file($data['full_path']);
}

/* read slashed file content
 * as uploaded by file upload library
 */
function read_file_content($data)
{
  return addslashes(read_raw_upload($data));
}

/* removes uploaded image */
function remove_image($data)
{
  unlink($data['full_path']);
}

/* read image and then delete it */
function read_file_and_delete($data)
{
  $content = read_file_content($data);
  remove_image($data);

  return $content;
}

function file_extension($file)
{
  $path_info = pathinfo($file);
  if(array_key_exists('extension', $path_info)) {
    return $path_info['extension'];
  } else {
    return '';
  }
}

function is_xml_file($file)
{
  return file_extension($file) == 'xml';
}

function find_executable($name)
{
  $normal_paths = array('/bin', '/usr/bin', '/usr/local/bin', '/opt/local/bin', '/usr/local/blast/bin');
  $path = '';
  
  foreach($normal_paths as $dir) {
    $full_path = "$dir/$name";
    if(is_executable($full_path)) {
      return $full_path;
    }
  }
  
  // seqret has not been found, use find
  $output = shell_exec("find / -name $name");
  foreach(explode("\n", $output) as $line) {
    $line = trim($line);
    if(is_executable($line)) {
      return $line;
    }
  }
  
  return null;
}

function generate_new_file_name()
{
  return tempnam(sys_get_temp_dir(), 'bio');
}

function write_raw_file($str)
{
  $temp_file = generate_new_file_name();
  $fp = fopen($temp_file, 'w');

  fwrite($fp, $str);
  fclose($fp);

  return $temp_file;
}

function write_file_export($seq_content)
{
  return write_raw_file($seq_content);
}

function load_ci_model($name)
{
  $CI =& get_instance();
  $CI->load->model($name, '', TRUE);
  
  return $CI->$name;
}

function split_string($string, $blocks, $separator)
{
  $size = strlen($string);
  
  if($size <= $blocks)
    return $string;

  $ret = '';
  for($i = 0; $i < $size; $i = $i + $blocks) {
    $ret .= substr($string, $i, $blocks) . $separator;
  }

  return $ret;
}

function generate_random($min = null, $max = null)
{
  srand((double)microtime() * 1000000);

  if(!$min) {
    $min = 0;
  }

  if(!$max) {
    $max = 32768;
  }

  return rand($max, $min);
}

function addmyslashes($str)
{
  return str_replace('\\', '\\\\', $str);
}

class FileObject
{
  private $name = null;
  private $content = null;
  
  function FileObject($name, $content)
  {
    $this->name = $name;
    $this->content = $content;
  }
  
  public function get_name()
  {
    return $this->name;
  }
  
  public function get_content()
  {
    return $this->content;
  }
}