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

