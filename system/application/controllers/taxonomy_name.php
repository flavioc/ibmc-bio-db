<?php

class Taxonomy_name extends BioController {
  function Taxonomy_name()
  {
    parent::BioController();
  }

  function list_all($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('taxonomy_name_model');
    $names = $this->taxonomy_name_model->get_tax($id);

    echo json_encode($names);
  }

  function edit_type_name()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = intval($this->input->post('value'));

    $this->load->model('taxonomy_name_model');

    $this->taxonomy_name_model->edit_type($id, $value);

    $this->load->model('taxonomy_name_type_model');

    $new_name = $this->taxonomy_name_type_model->get_name($value);

    echo $new_name;
  }

  function edit_name()
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->library('input');

    $id_str = $this->input->post('id');
    $id = parse_id($id_str);
    $value = $this->input->post('value');

    $this->load->model('taxonomy_name_model');

    $size_value = strlen($value);

    if($size_value < 3 || $size_value > 512) {
      echo $this->taxonomy_name_model->get_name($id);
      return;
    }

    $this->taxonomy_name_model->edit_name($id, $value);

    echo $value;
  }

  function add($tax, $name, $type)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('taxonomy_name_model');

    $id = $this->taxonomy_name_model->add(intval($tax), $name, intval($type));

    echo json_encode($this->taxonomy_name_model->get_name_and_type($id));
  }

  function delete($id)
  {
    if(!$this->logged_in) {
      return;
    }

    $this->load->model('taxonomy_name_model');

    $this->taxonomy_name_model->delete(intval($id));

    echo json_encode(true);
  }
}
