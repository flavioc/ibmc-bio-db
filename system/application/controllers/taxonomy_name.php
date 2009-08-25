<?php

class Taxonomy_name extends BioController
{
  function Taxonomy_name()
  {
    parent::BioController();
  }

  public function list_all($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $this->load->model('taxonomy_name_model');
    $names = $this->taxonomy_name_model->get_tax($id);

    $this->json_return($names);
  }

  public function edit_type_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id_str = $this->get_post('id');
    $id = parse_id($id_str);
    $value = intval($this->get_post('value'));

    $this->load->model('taxonomy_name_model');

    $this->taxonomy_name_model->edit_type($id, $value);

    $this->load->model('taxonomy_name_type_model');

    $new_name = $this->taxonomy_name_type_model->get_name($value);

    echo $new_name;
  }

  public function edit_name()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id_str = $this->get_post('id');
    $id = parse_id($id_str);
    $value = $this->get_post('value');

    $this->load->model('taxonomy_name_model');
    $this->taxonomy_name_model->edit_name($id, $value);

    echo $this->taxonomy_name_model->get_name($id);
  }

  public function add($tax, $name, $type)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->load->model('taxonomy_name_model');

    $id = $this->taxonomy_name_model->add(intval($tax), $name, intval($type));

    $this->json_return($this->taxonomy_name_model->get_name_and_type($id));
  }

  public function delete($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->load->model('taxonomy_name_model');

    $this->taxonomy_name_model->delete(intval($id));

    $this->json_return(true);
  }
}