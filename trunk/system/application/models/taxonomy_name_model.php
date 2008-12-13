<?php

class Taxonomy_name_model extends BioModel
{
  function Taxonomy_name_model()
  {
    parent::BioModel('taxonomy_name');
  }

  function get($id)
  {
    return $this->get_id($id);
  }

  function get_tax($tax_id)
  {
    return $this->get_all_by_field('tax_id', $tax_id, 'taxonomy_name_and_type');
  }

  function get_tax_id($id)
  {
    return $this->get_field($id, 'tax_id');
  }

  function edit_type($id, $type_id)
  {
    $tax = $this->get_tax_id($id);

    $this->db->trans_start();

    $this->edit_field($id, 'type_id', $type_id);
    $this->update_history($tax, 'taxonomy');

    $this->db->trans_complete();
  }

  function edit_name($id, $name)
  {
    $tax = $this->get_tax_id($id);

    $this->db->trans_start();

    $this->edit_field($id, 'name', $name);
    $this->update_history($tax, 'taxonomy');

    $this->db->trans_complete();
  }

  function get_name($id)
  {
    return $this->get_field($id, 'name');
  }

  function add($tax, $name, $type)
  {
    $data = array(
      'tax_id' => $tax,
      'name' => $name,
      'type_id' => $type,
    );

    $this->db->trans_start();

    $ret = $this->insert_data($data);
    $this->update_history($tax, 'taxonomy');

    $this->db->trans_complete();

    return $ret;
  }

  function delete($id)
  {
    $this->delete_id($id);
  }
}
