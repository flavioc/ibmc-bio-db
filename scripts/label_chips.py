#!/usr/bin/python

from label_common import *

add_label(name = "nc_codon",
    type = "float",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$this->load->library('Chips'); $this->chips->run_seq($content); return $this->chips->get_codon_usage_statistic();",
    deletable = True,
    editable = False)
