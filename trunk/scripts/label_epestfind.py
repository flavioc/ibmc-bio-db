#!/usr/bin/python

from label_common import *

add_label(name = "proteolytic_cleavage_sites",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Epestfind'); $this->epestfind->run_seq($content); return $this->epestfind->get_proteolytic_cleavage_sites();",
    deletable = True,
    editable = False)
