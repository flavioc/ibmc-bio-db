
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

function toggle_menu(what) {
  var id = what + '_id';
  var menu = what + '_menu';
  var visible_session = what + '_menu_visible';

  var all = $('#' + id);
  var is_visible = $.session(visible_session);
  if(is_visible == true) {
    all.show();
  } else {
    all.hide();
  }

  $('#' + menu).click(function (event) {
    var obj_id = $('#' + id);
    if(all.is(':hidden')) {
        obj_id.slideDown();
        $.session(visible_session, true);
      } else {
        obj_id.slideUp();
        $.session(visible_session, false);
      }
    });
}
