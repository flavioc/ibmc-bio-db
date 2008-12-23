<?php

$system_folder = "../system";
require_once('ci_model_remote_open.php');
require_once(APPPATH . "/models/taxonomy_rank_model.php");

$CI->load->model('taxonomy_rank_model');
$CI->load->model('taxonomy_model');
$CI->load->model('taxonomy_name_model');
$CI->load->model('taxonomy_name_type_model');

$taxdump_dir = "../../taxdump";
$taxdump_nodes = "$taxdump_dir/nodes.dmp";
$taxdump_names = "$taxdump_dir/names.dmp";

class ReadFile
{
  var $fp = null;
  var $data = null;
  var $alldata = null;
  var $saveddata = null;

  function ReadFile($name)
  {
    global $taxdump_dir;
    $fullname = "$taxdump_dir/$name.dmp";
    $this->fp = fopen($fullname, "r");
  }

  function readNext()
  {
    $line = fgets($this->fp);

    if($line == null) {
      return false;
    }

    $this->data = split("\|", $line);
    if(count($this->data) < 3) {
      return false;
    }

    $this->data = array_map('trim', $this->data);

    return true;
  }

  function getData($i)
  {
    return $this->data[$i];
  }

  function addNewData($data)
  {
    $this->alldata[] = $data;
  }

  function readAllNext($id)
  {
    $this->alldata = array();
    if($this->saveddata) {
      $this->addNewData($this->saveddata);
      $this->saveddata = null;
    }

    while($this->readNext()) {
      $got_id = $this->getData(0);
      $int_id = intval($got_id);

      if($int_id < $id) {
        continue;
      } else if($int_id == $id) {
        $this->addNewData($this->data);
      } else {
        $this->saveddata = $this->data;
        break;
      }
    }

    return $this->hasMoreItems();
  }

  function totalItems()
  {
    return count($this->alldata);
  }

  function hasMoreItems()
  {
    if($this->alldata == null) {
      return false;
    }

    return $this->totalItems() > 0;
  }

  function getAllItems()
  {
    $ret = $this->alldata;

    $this->alldata = null;

    return $ret;
  }

  function close()
  {
    fclose($this->fp);
  }
}

function is_scientific_name($el)
{
  return $el[3] == "scientific name";
}

function get_scientific_name($data)
{
  foreach($data as $el) {
    if(is_scientific_name($el)) {
      return $el;
    }
  }

  return null;
}

$nodes = new ReadFile("nodes");
$names = new ReadFile("names");

$cnt = 0;
while($nodes->readNext()) {
  $id_str = $nodes->getData(0);
  $id = intval($id_str);

  if($names->readAllNext($id)) {
    $parent_id = intval($nodes->getData(1));
    $rank = $nodes->getData(2);
    $rank_id = $CI->taxonomy_rank_model->get_rank_id($rank);

    $names_data = $names->getAllItems();

    $scientific_name = get_scientific_name($names_data);

    $real_id = $CI->taxonomy_model->ensure_existance($id, $parent_id,
      $rank_id, $scientific_name[1]);

    // adicionar nomes
    foreach($names_data as $name) {
      if(!is_scientific_name($name)) {
        $tipo = $name[3];
        $type_id = $CI->taxonomy_name_type_model->get_type_id($tipo);

        $CI->taxonomy_name_model->ensure_existance($real_id,
          $name[1], $type_id);
      }
    }

    ++$cnt;
    print("\r\b=> $cnt");
  }

  /*if($cnt == 3) {
    break;
  }*/
}

$names->close();
$nodes->close();

echo "\nNOW FIXING PARENTS...\n";
$taxonomies_bad_parents = $CI->taxonomy_model->get_import_parented();

$cnt = 0;
foreach($taxonomies_bad_parents as $tax)
{
  $id = $tax['id'];
  $imported_parent = $tax['import_parent_id'];

  $CI->taxonomy_model->fix_imported_parent($id, $imported_parent);

  ++$cnt;
  print("\r\b=> $cnt");
}

require_once('ci_model_remote_close.php');

?> 
