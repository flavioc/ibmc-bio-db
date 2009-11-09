<?php

class Event_Model extends BioModel
{
  function Event_Model()
  {
    parent::BioModel('event');
  }
  
  public function add($code)
  {
    $data = array(
      'code' => $code,
    );
    
    return $this->insert_data($data);
  }
  
  public function remove($code)
  {
    return $this->delete_by_field('code', $code);
  }
  
  public function get($code)
  {
    $row = $this->get_row('code', $code);
    
    if(!$row)
      return null;
      
    $ret = $row['data'];
    
    if(!$ret || $ret == '')
      return null;
      
    return json_decode($ret, true);
  }
  
  public function set($code, $val)
  {
    $this->db->where('code', $code);
    
    return $this->db->update($this->table, array('data' => json_encode($val)));
  }
}