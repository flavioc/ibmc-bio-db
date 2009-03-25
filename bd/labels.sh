#!/bin/sh

USERNAME=fdb_app
PASSWORD=$1
DATABASE=FDB
HOSTNAME=localhost

function add_label()
{
	type=$1
	name=$2
	autoadd=$3
	must_exist=$4
	auto_on_creation=$5
	auto_on_modification=$6
	code=$7
  valid_code=$8
	deletable=$9
	editable=${10}
	multiple=${11}

	mysql -h $HOSTNAME -u $USERNAME -D $DATABASE --password=$PASSWORD -e "INSERT INTO label(type, name, autoadd, \`default\`, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple) VALUES('$type', \"$name\", $autoadd, TRUE, $must_exist, $auto_on_creation, $auto_on_modification, \"$code\", \"$valid_code\", $deletable, $editable, $multiple)"
}

add_label "integer" "length" "TRUE" "TRUE" \
	"TRUE" "TRUE" "return strlen(\$content);" \
  "return \$data > 0;" \
	"FALSE" "FALSE" "FALSE"

add_label "ref" "refseq" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" \
  "return \$data > 0;" \
	"TRUE" "TRUE" "FALSE"

add_label "position" "refpos" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" "" \
	"TRUE" "TRUE" "FALSE"

add_label "url" "url" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" "" \
	"TRUE" "TRUE" "TRUE"

add_label "integer" "internal_id" "TRUE" "TRUE" \
	"TRUE" "FALSE" "return \$id;" \
  "return \$data > 0;" \
	"FALSE" "FALSE" "FALSE"

add_label "bool" "perm_public" "TRUE" "TRUE" \
  "TRUE" "FALSE" "return false;" "" \
  "FALSE" "TRUE" "FALSE"
	
add_label "text" "type" "TRUE" "TRUE" \
  "TRUE" "TRUE" "return sequence_type(\$content);" \
  "return \$data == 'dna' || \$data == 'protein';" \
  "FALSE" "TRUE" "FALSE"
