<?php

// simple file line reader. supports one line rewind
class LineReader
{
  private $fp = null;
  private $unread_line = null;
  private $current_line = 0;
  
  function LineReader($file)
  {
    $this->fp = fopen($file, 'rb');
  }
  
  public function get_line_number()
  {
    return $this->current_line;
  }
  
  public function get_line()
  {
    ++$this->current_line;
    
    if($this->unread_line) {
      $ret = $this->unread_line;
      
      $this->unread_line = null;
      
      return $ret;
    }
    
    return trim(fgets($this->fp));
  }
  
  public function unread_line($line)
  {
    --$this->current_line;
    $this->unread_line = $line;
  }
  
  public function ends()
  {
    return feof($this->fp);
  }
  
  public function finish()
  {
    fclose($this->fp);
  }
}