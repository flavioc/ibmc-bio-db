
function parse_id(str) {
  return str.split('_')[1];
}

function is_ok(data) {
  return data.length >= 2 && data.substring(0, 2) == 'ok';
}

function build_edit_id(type, id) {
  return type + 'edit_' + id;
}

function build_delete_id(type, id) {
  return type + 'delete_' + id;
}

function build_tr_id(id) {
  return 'tr_' + id;
}

function show_load() {
  $('#loader').show();
}

function hide_load() {
  $('#loader').hide();
}
