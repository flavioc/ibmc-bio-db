<?php


/* resizes image before putting it on the DB.
 * $data is the array returned by file upload library.
 */
function process_user_image($controller, $data)
{
  $config['image_library'] = 'gd2';
  $config['source_image'] = $data['full_path'];
  $config['maintain_ratio'] = TRUE;
  $config['width'] = 300;
  $config['height'] = 300;

  $controller->load->library('image_lib', $config); 

  $controller->image_lib->resize();
}

/* read slashed file content
 * as uploaded by file upload library
 */
function read_file_content($data)
{
  $path = $data['full_path'];
  $fp = fopen($path, 'rb');
  $size = filesize($path);

  $content = fread($fp, $size);

  fclose($fp);

  $content = addslashes($content);

  return $content;
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

/* process image from database to gd format */
function process_db_image($content)
{
  return imagecreatefromstring(stripslashes($content));
}

