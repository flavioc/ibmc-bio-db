<?php

class Label_Sequence extends BioController {
  function Label_Sequence()
  {
    parent::BioController();
    $this->load->model('sequence_model');
    $this->load->model('label_model');
    $this->load->model('label_sequence_model');
  }

  function get_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $labels = $this->label_sequence_model->get_sequence($id);

    $this->json_return($labels);
  }

  function get_missing_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_missing_obligatory($id);

    $this->json_return($data);
  }

  function get_addable_labels($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_addable_labels($id);

    $this->json_return($data);
  }

  function get_validation_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_validation_labels($id);

    $this->json_return($data);
  }

  function get_bad_multiple_labels($id)
  {
    if(!$this->logged_in && !$this->sequence_model->permission_public($id)) {
      return $this->invalid_permission_empty();
    }

    $data = $this->label_sequence_model->get_bad_multiple($id);

    $this->json_return($data);
  }

  function download_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $label = $this->label_sequence_model->get_id($id);

    $name = $label['text_data'];
    $data = $label['obj_data'];

    header("Content-Disposition: attachment; filename=\"$name\"");

    echo stripslashes($label['obj_data']);
  }

  function edit_subname() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id_str = $this->get_post('id');
    $id = parse_id($id_str);
    $value = $this->get_post('value');

    $result = $this->label_sequence_model->edit_subname($id, $value);

    if($value) {
      echo $value;
    } else {
      $this->return_empty();
    }
  }

  function delete_label($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->json_return($this->label_sequence_model->delete($id));
  }
}
