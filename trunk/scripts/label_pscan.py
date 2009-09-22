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
