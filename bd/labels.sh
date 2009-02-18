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
	deletable=$8
	editable=$9
	multiple=${10}

	mysql -h $HOSTNAME -u $USERNAME -D $DATABASE --password=$PASSWORD -e "INSERT INTO label(type, name, autoadd, \`default\`, must_exist, auto_on_creation, auto_on_modification, code, deletable, editable, multiple) VALUES('$type', \"$name\", $autoadd, TRUE, $must_exist, $auto_on_creation, $auto_on_modification, \"$code\", $deletable, $editable, $multiple)"
}

add_label "integer" "length" "TRUE" "TRUE" \
	"TRUE" "TRUE" "return strlen(\$content);" \
	"FALSE" "FALSE" "FALSE"

add_label "ref" "refseq" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" \
	"TRUE" "TRUE" "FALSE"

add_label "position" "refpos" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" \
	"TRUE" "TRUE" "FALSE"

add_label "url" "url" "FALSE" "FALSE" \
	"FALSE" "FALSE" "" \
	"TRUE" "TRUE" "TRUE"
	
