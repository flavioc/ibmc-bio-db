<?php

class RankImporter
{
  private $model = null;
  
  function RankImporter()
  {
    $this->model = load_ci_model('taxonomy_rank_model');
  }
  
  function import_xml($file)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      return null;
    }

    $top = $xmlDoc->documentElement;

    if(!$top) {
      return null;
    }

    if($top->nodeName != 'ranks') {
      return null;
    }

    $ranks = array();

    foreach($top->childNodes as $child)
    {
      if($child->nodeName != 'rank') {
        continue;
      }

      $name_node = find_xml_child($child, 'name');
      if(!$name_node) {
        continue;
      }

      $name = trim(xmlspecialchars_decode($name_node->textContent));
      if(!$name) {
        continue;
      }

      $parent_node = find_xml_child($child, 'parent');
      if($parent_node) {
        $parent = trim(xmlspecialchars_decode($parent_node->textContent));
      } else {
        $parent = '';
      }

      array_push($ranks, array('rank_name' => $name,
                               'rank_parent_name' => $parent));
    }

    if(count($ranks) == 0) {
      return null;
    }

    // insert names
    foreach($ranks as &$rank) {
      $name = $rank['rank_name'];

      if($this->model->has_name($name)) {
        $rank['id'] = $this->model->get_id_name($name);
        $rank['mode'] = 'edit';
      } else {
        $rank['mode'] = 'add';
        $id = $this->model->add($name);
        $rank['id'] = $id;
      }
    }

    // insert parents
    foreach($ranks as &$rank) {
     $id = $rank['id'];

     if($id) {
       $parent_name = $rank['rank_parent_name'];
       $parent_id = $this->model->get_id_name($parent_name);

       if($parent_id) {
         $rank['parent_found'] = true;
         $rank['parent_id'] = $parent_id;
         $this->model->edit_parent($id, $parent_id);
       } else {
         $rank['parent_found'] = false;
       }
     }
    }

    return $ranks;
  }
}