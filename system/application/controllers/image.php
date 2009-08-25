<?php

class Image extends BioController
{
  function Image()
  {
    parent::BioController();	
    $this->load->model('user_model');
  }

  private function __post_image($image, $percent)
  {
    header('Content-type: image/png');

    $percent = $percent / 100.0;
    $width = imagesx($image);
    $height = imagesy($image);
    $newwidth = $width * $percent;
    $newheight = $height * $percent;

    $thumb = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresized($thumb, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    imagepng($thumb);
    imagedestroy($image);
    imagedestroy($thumb);
  }
	
  public function get_name($name, $resize = 100)
  {
    $image = $this->user_model->get_user_image_by_name($name);

    if($image == null) {
      return;
    }

    $this->__post_image($image, intval($resize));
  }

  public function get_id($id, $resize = 100)
  {
    $image = $this->user_model->get_user_image_by_id($id);

    if($image == null) {
      return;
    }

    $this->__post_image($image, intval($resize));
  }
}

