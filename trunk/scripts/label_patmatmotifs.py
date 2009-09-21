#!/usr/bin/python

from label_common import *

add_label(name = "motifs_prosite",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Patmatmotifs'); $this->patmatmotifs->run_seq($content); return $this->patmatmotifs->get_number_motifs();",
    deletable = True,
    editable = False)

add_label(name = "motif_position",
    type = "position",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Patmatmotifs'); $this->patmatmotifs->run_seq($content); return $this->patmatmotifs->get_motifs_positions();",
    deletable = True,
    editable = False)

add_label(name = "motif_prosite",
    type = "text",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Patmatmotifs'); $this->patmatmotifs->run_seq($content); return $this->patmatmotifs->get_motifs();",
    deletable = True,
    editable = False)
