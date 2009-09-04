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

/* process image from database to gd format */
function process_db_image($content)
{
  return imagecreatefromstring(stripslashes($content));
}