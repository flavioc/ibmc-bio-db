#!/usr/bin/python

from label_common import *

add_label(name = "isoelectric_point",
    type = "float",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('IEP'); $this->iep->run_seq($content); return $this->iep->get_isoelectric_point();",
    deletable = True,
    editable = False)
