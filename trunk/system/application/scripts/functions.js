
function parse_id(str) {
  var vec = str.split('_');

  return vec[vec.length-1];
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

function is_yes(data)
{
  return data == 'yes';
}

function enable_error_messages()
{
  var obj = $(".hide_box");

  var fun = function (event) {
    $(event.target).parent().fadeOut("slow");
  }

  obj.unbind("click").click(fun);
}

function add_new_error_message(msg)
{
  image_url = get_images_url();
  $("#content").prepend("<div class=\"error_msg\"><img src=\"" + image_url +
      "/error.png\"></img>" + msg + "<a class=\"hide_box\" href=\"#\">Hide</a></div>");
  enable_error_messages();
}

var base_url_cache = null;

function get_base_url()
{
  if(base_url_cache != null) {
    return base_url_cache;
  }

  var url = window.location.href;
  var vec = url.split("/");
  var ret = "";

  for(var i = 0; i < vec.length; ++i) {
      if(vec[i] == "index.php") {
        base_url_cache = ret;
        return ret;
      } else {
        if(ret != "") {
          ret += "/";
        }

        ret += vec[i];
      }
  }

  return url;
}

function get_images_url()
{
  return get_base_url() + "/images";
}

function get_app_url()
{
  return get_base_url() + "/index.php";
}

function get_url_id()
{
  var url = window.location.href;
  var vec = url.split(/\/|#/);

  for(var i = vec.length-1; i >= 0; --i) {
      var num = parseInt(vec[i]);

      if(num.toString() != "NaN") {
        return num;
      }
  }

  return 0;
}

function checkbox_enabled(dom)
{
  var obj = $(dom);

  return obj.size() != 0 && obj.is(":checked");
}

function get_paging_size()
{
  return parseInt($.cookie('paging-size'));
}

function get_logged_in()
{
  var value = $.cookie('logged-in');

  if(value == 'deleted') {
    return false;
  }

  if(value == '1') {
    return true;
  }

  return false;
}

function build_class_text(class_name)
{
  if(class_name == null) {
    return '';
  } else {
    return ' class="' + class_name + '" ';
  }
}

function build_image_url(url, class_name)
{
  var class_txt = build_class_text(class_name);
  return '<img src="' + get_images_url() + '/' + url + '"' + class_txt + ' />';
}

function build_href(url, class_name, inner)
{
  var class_txt = build_class_text(class_name);
  return '<a href="' + url + '" ' + class_txt + '>' + inner + '</a>';
}

function activate_delete_dialog(url, delete_button, form_delete)
{
  if(delete_button == null) {
    delete_button = '#delete_button';
  }
  if(form_delete == null) {
    form_delete = '#form_delete';
  }

  $(delete_button).click(function () {
    $.ajaxprompt(url,
      {
        buttons: {Yes: true, No: false},
        submit: function (v) {
          if(v) {
            $(form_delete).submit();
          }

          return true;
        }
      });
    return false;
  });
}

function build_user_url(id)
{
  return get_app_url() + '/profile/view/' + id;
}

function sql_true(val)
{
  return val == '1';
}

function sql_false(val)
{
  return val == '0';
}

function is_numeric(val)
{
  return !isNaN(val);
}

function is_valid_url(val)
{
  var v = new RegExp();
  v.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
  return v.test(val);
} 
