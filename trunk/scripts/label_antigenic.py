#!/usr/bin/python

from label_common import *

add_label(name = "antigenic_sites",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Antigenic'); $this->antigenic->run_seq($content); return $this->antigenic->get_number_antigenic_sites();",
    deletable = True,
    editable = False)
