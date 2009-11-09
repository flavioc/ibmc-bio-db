<?php

class Label_Sequence extends BioController
{
  function Label_Sequence()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }

  public function get_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $filter_name = $this->get_parameter('name');
    $filter_type = $this->get_parameter('type');
    $filter_user = $this->get_parameter('user');
    
    $labels = $this->label_sequence_model->get_sequence($id,
      array('name' => $filter_name, 'type' => $filter_type, 'user' => $filter_user));

    $this->json_return($labels);
  }

  public function get_missing_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_missing_obligatory($id);

    $this->json_return($data);
  }

  public function get_addable_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }
    
    $filter_name = $this->get_parameter('name');
    $filter_type = $this->get_parameter('type');
    $filter_user = $this->get_parameter('user');

    $data = $this->label_sequence_model->get_addable_labels($id,
      array('name' => $filter_name,
            'type' => $filter_type,
            'user' => $filter_user));

    $this->json_return($data);
  }

  public function get_bad_multiple_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_bad_multiple($id);

    $this->json_return($data);
  }

  public function delete_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->json_return($this->label_sequence_model->delete($id));
  }
}