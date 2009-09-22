#!/usr/bin/python

from label_common import *

add_label(name = "prints_fingerprint",
    type = "text",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprints();",
    deletable = True,
    editable = False)

add_label(name = "prints_fingerprint_elements",
    type = "integer",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprint_elements();",
    deletable = True,
    editable = False)

add_label(name = "prints_fingerprint_accession",
    type = "text",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprint_accession();",
    deletable = True,
    editable = False)

add_label(name = "prints_fingerprint_threshold",
    type = "float",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprint_thresholds();",
    deletable = True,
    editable = False)

add_label(name = "prints_fingerprint_score",
    type = "float",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprint_scores();",
    deletable = True,
    editable = False)

add_label(name = "prints_fingerprint_position",
    type = "position",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('PScan'); $this->pscan->run_seq($content); return $this->pscan->get_prints_fingerprint_positions();",
    deletable = True,
    editable = False)
