<?php

class Label_sequence_model extends BioModel
{
  function Label_sequence_model() {
    parent::BioModel('label_sequence');
  }

  function __get_data_fields($type)
  {
    switch($type) {
    case "integer":
      return "int_data";
    case "text":
      return "text_data";
    case "obj":
      return "obj_data";
    case "position":
      return array("position_a_data", "position_b_data");
    case "ref":
      return "ref_data";
    case "tax":
      return "tax_data";
    case "url":
      return "url_data";
    }

    return "";
  }
}
