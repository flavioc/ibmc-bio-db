#!/usr/bin/python

from label_common import *

add_label(name = "length",
    type = "integer",
    code = "return strlen($content);",
    valid_code = "return $data > 0;")

add_label(name = "refseq",
    type = "ref",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = False,
    valid_code = "return $data > 0;",
    deletable = True,
    editable = True)

add_label(name = "refpos",
    type = "position",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = False,
    deletable = True,
    editable = True)

add_label(name = "url",
    type = "url",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = False,
    deletable = True,
    editable = True,
    multiple = True)

add_label(name = "internal_id",
    type = "integer",
    auto_on_modification = False,
    code = "return $id;",
    valid_code = "return $data > 0;")

add_label(name = "perm_public",
    type = "bool",
    auto_on_modification = False,
    code = "return false;",
    editable = True)

add_label(name = "type",
    type = "text",
    code = "return sequence_type($content);",
    valid_code = "return valid_sequence_type($data);",
    editable = True)

add_label(name = "name",
    type = "text",
    auto_on_creation = True,
    auto_on_modification = False,
    editable = True,
    multiple = False,
    must_exist = True,
    default = True)

add_label(name = "content",
    type = "text",
    auto_on_creation = True,
    auto_on_modification = True,
    editable = True,
    multiple = False,
    must_exist = True,
    default = True)

add_label(name = "creation_user",
    type = "text",
    auto_on_creation = True,
    auto_on_modification = False,
    editable = False,
    multiple = False,
    must_exist = False,
    default = True)

add_label(name = "update_user",
    type = "text",
    auto_on_creation = True,
    auto_on_modification = True,
    editable = False,
    multiple = False,
    must_exist = False,
    default = True)

add_label(name = "creation_date",
    type = "date",
    auto_on_creation = True,
    auto_on_modification = False,
    editable = False,
    multiple = False,
    must_exist = True,
    default = True)

add_label(name = "update_date",
    type = "date",
    auto_on_creation = True,
    auto_on_modification = True,
    editable = False,
    multiple = False,
    must_exist = True,
    default = True)

add_label(name = "translated",
    type = "ref",
    valid_code = "$type1 = $seq_model->get_type($id); $type2 = $seq_model->get_type($data); return valid_sequence_type($type1) && valid_sequence_type($type2) && $type1 != $type2;",
    code = "$this->load->model('sequence_model'); $type = $this->sequence_model->get_type($id); if($type != 'dna') return null; $this->load->library('SequenceConverter'); $protein = $this->sequenceconverter->convert_dna_sequence($name, $content); $this->load->library('SequenceImporter'); $info = $this->sequenceimporter->import_fasta($protein); unlink($protein); list($seqs, $labels) = $info->import(); $new_sequence = $seqs[0]; $this->sequence_model->set_translated_sequence($id, $new_sequence['id']); return $new_sequence['id'];",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    multiple = False,
    must_exist = False,
    deletable = True,
    default = True)

add_label(name = "super",
    type = "ref",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = False,
    multiple = False,
    must_exist = False,
    deletable = False,
    default = True)

add_label(name = "super_position",
    type = "position",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = False,
    must_exist = False,
    deletable = False,
    multiple = False,
    default = True)

add_label(name = "immutable",
    type = "bool",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = True)

add_label(name = "subsequence",
    type = "ref",
    auto_on_creation = False,
    auto_on_modification = False,
    action_modification = "$infos = $this->get_label_infos($id, $label_id);\
    foreach($infos as &$info) {\
      $sub_id = label_get_type_data($info);\
      $sequence_model->delete($sub_id); \
    } \
    $this->remove_labels_sequence($label_id, $id);",
    editable = False,
    must_exist = False,
    deletable = False,
    multiple = True,
    default = True)

add_label(name = "lifetime",
    type = "date",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = False,
    must_exist = False,
    deletable = False,
    multiple = False,
    default = True)

add_label(name = "file",
    type = "obj",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = True)

db.close()

