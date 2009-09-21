#!/usr/bin/python

from label_common import *

add_label(name = "pest_motifs",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_proteolytic_cleavage_sites();",
    deletable = True,
    editable = False)

add_label(name = "pest_position",
    type = "position",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_positions();",
    deletable = True,
    multiple = True,
    editable = False)

add_label(name = "pest_amino_acid",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_amino_acids();",
    deletable = True,
    multiple = True,
    editable = False)

add_label(name = "pest_type",
    type = "text",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_types();",
    deletable = True,
    multiple = True,
    editable = False)

add_label(name = "pest_score",
    type = "float",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_scores();",
    deletable = True,
    multiple = True,
    editable = False)

add_label(name = "pest_depst",
    type = "float",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_depsts();",
    deletable = True,
    multiple = True,
    editable = False)

add_label(name = "pest_hydrophobicity",
    type = "float",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_pest_hydrophobicity();",
    deletable = True,
    multiple = True,
    editable = False)
