<?php

class File_model extends BioModel
{
  function File_model()
  {
    parent::BioModel('file');
  }
  
  private function update($id, $content, $type = NULL)
  {
    $data = array(
      'data' => $content,
    );
    
    if($type)
      $data['type'] = $type;
    
    if($this->edit_data($id, $data))
      return $id;
    
    return null;
  }
  
  private function get_count($id)
  {
    return (int)$this->get_field($id, 'count');
  }
  
  public function remove($id)
  {
    return $this->delete_id($id);
  }
  
  public function delete_by_name($name, $label_id)
  {
    $id = $this->get_id($name, $label_id);
    
    if($id)
      return $this->delete($id);
      
    return null;
  }
  
  public function add($name, $content, $label_id = null, $type = null)
  {
    $content = addslashes($content);
    
    if($this->has($name, $label_id)) {
      $id = $this->get_id($name, $label_id);
      return $this->update($id, $content, $type);
    }
    
    $data = array(
      'name' => $name,
      'label_id' => $label_id,
      'data' => $content,
      'type' => $type,
    );
     
    return $this->insert_data($data); 
  }
  
  public function has($name, $label_id = null)
  {
    $this->db->where('name', $name);
    $this->db->where('label_id', $label_id);
    
    return $this->has_something();
  }
  
  public function get_id($name, $label_id = null)
  {
    $this->db->where('label_id', $label_id);
    
    return $this->get_id_by_field('name', $name);
  }
  
  public function get($id)
  {
    $data =  parent::get_id($id);
    $data['data'] = stripslashes($data['data']);
    
    return $data;
  }
  
  public function get_label_files($label_id)
  {
    $this->db->select('id, name');
    $this->db->where('label_id', $label_id);
    
    return parent::get_all();
  }
  
  public function delete_all_labels()
  {
    $this->db->where('label_id IS NOT NULL');
    
    return $this->delete_rows();
  }
  
  public function add_background($data, $type)
  {
    return $this->add('background', $data, null, $type);
  }
  
  public function has_background()
  {
    return $this->has('background', NULL);
  }
  
  public function get_background()
  {
    return $this->get($this->get_id('background', NULL));
  }
  
  public function get_background_extension()
  {
    $file = $this->get_background();
  
    return $file['type'];
  }
  
  public function remove_background()
  {
    $id = $this->get_id('background', NULL);
    
    if($id)
      $this->remove($id);
  
    return $id != NULL;
  }
}