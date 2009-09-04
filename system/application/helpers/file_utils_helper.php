<?php

function read_file_line($file)
{
  return trim(fgets($file));
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

function find_executable($name)
{
  $normal_paths = array('/bin', '/usr/bin', '/usr/local/bin', '/opt/local/bin');
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