<?php

class Sequence_model extends BioModel
{
  function Sequence_model() {
    parent::BioModel('sequence');
  }

  function get_all($start = null, $size = null)
  {
    $this->db->order_by('name');

    if($start != null && $size != null) {
      $this->db->limit($size, $start);
    }

    return parent::get_all();
  }

  function get_total()
  {
    return $this->count_total();
  }

  function add($name, $accession, $type, $content) {
    $data = array(
      'name' => $name,
      'accession' => $accession,
      'type' => $type,
      'content' => $content,
    );

    return $this->insert_data_with_history($data);
  }

  function get($id)
  {
    return $this->get_id($id);
  }

  function delete($id)
  {
    $this->delete_id($id);
  }

  function get_content($id)
  {
    return $this->get_field($id, 'content');
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function edit_name($id, $name)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'name', $name);

    $this->db->trans_complete();
  }

  function edit_accession($id, $accession)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'accession', $accession);

    $this->db->trans_complete();
  }

  function edit_content($id, $content)
  {
    $this->db->trans_start();

    $this->update_history($id);
    $this->edit_field($id, 'content', $content);
    $label_sequence = $this->load_model('label_sequence_model');
    $label_sequence->regenerate_labels($id);

    $this->db->trans_complete();
  }
}
