#!/usr/bin/python

from label_common import *

add_label(name = "species",
    type = "tax",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "gene_start",
    type = "integer",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "gene_end",
    type = "integer",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "chromosome",
    type = "text",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "strand",
    type = "text",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "label_other1",
    type = "text",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

add_label(name = "label_other2",
    type = "text",
    auto_on_creation = False,
    auto_on_modification = False,
    editable = True,
    must_exist = False,
    deletable = True,
    multiple = False,
    default = False)

db.close()
