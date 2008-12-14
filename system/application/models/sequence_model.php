<?php

class Sequence_model extends BioModel
{
  function Sequence_model() {
    parent::BioModel('sequence');
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
    $this->db->select('id, name, accession, type');

    $query = $this->db->get_where($this->table, array('id' => $id));

    if($query == null || $query->num_rows() != 1) {
      return null;
    }

    return $query->row_array();
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
}
