#!/usr/bin/python

from label_common import *

add_label(name = "sigcleave_sites",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('SigCleave'); $this->sigcleave->run_seq($content); return $this->sigcleave->get_number_signal_cleavage_sites();",
    deletable = True,
    editable = False)

add_label(name = "sigcleave_position",
    type = "position",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('SigCleave'); $this->sigcleave->run_seq($content); return $this->sigcleave->get_sigcleave_positions();",
    deletable = True,
    editable = False)

add_label(name = "sigcleave_score",
    type = "float",
    multiple = True,
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('SigCleave'); $this->sigcleave->run_seq($content); return $this->sigcleave->get_sigcleave_scores();",
    deletable = True,
    editable = False)
