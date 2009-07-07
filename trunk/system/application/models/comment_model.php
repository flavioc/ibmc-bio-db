<?php

class Comment_model extends Model
{
  private static $comment_file = "../application/data/comment.txt";

  function Comment_model()
  {
    parent::Model();
  }

  function get()
  {
    $file = BASEPATH . self::$comment_file;

    $size = filesize($file);
    if($size == 0) {
      return '';
    }

    $fh = fopen($file, "rb");
    $data = fread($fh, $size);
    fclose($fh);

    return $data;
  }

  function set($comment)
  {
    $file = BASEPATH . self::$comment_file;

    $fh = fopen($file, "w");

    fwrite($fh, $comment);

    fclose($fh);
  }
}
