#!/usr/bin/python

from label_common import *

add_label(name = "letters",
    type = "integer",
    must_exist = False,
    auto_on_creation = False,
    auto_on_modification = True,
    code = "$letters = array(); for($i = 0; $i < strlen($content); ++$i) { $letter = $content[$i]; if(array_key_exists($letter, $letters)) { $letters[$letter] = $letters[$letter] + 1; } else { $letters[$letter] = 1; } } $ret = array(); foreach($letters as $letter => $total) { $ret[] = new LabelData($total, $letter); } return $ret;",
    deletable = True,
    multiple = True,
    editable = False)
