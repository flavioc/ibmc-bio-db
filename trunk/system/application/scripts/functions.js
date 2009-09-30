function non_empty_string(str)
{
  return typeof(str) == "string" && str.length > 0;
}

function rtrim(str, charlist)
{
  // http://kevin.vanzonneveld.net
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +      input by: Erkekjetter
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // *     example 1: rtrim('    Kevin van Zonneveld    ');
  // *     returns 1: '    Kevin van Zonneveld'
 
  charlist = !charlist ? ' \\s\u00A0' : (charlist+'').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
  var re = new RegExp('[' + charlist + ']+$', 'g');
  return (str+'').replace(re, '');
}

function parse_id(str)
{
  var vec = str.split('_');

  return vec[vec.length-1];
}

function is_yes(data)
{
  return data == 'yes';
}

var hide_box_html = '<a class="hide_box" href="#">Hide</a>';

function add_new_error_message(msg)
{
  image_url = get_images_url();
  $("#content").prepend('<div class="error_msg"><img src="' + image_url +
      '/error.png"></img>' + msg + hide_box_html + '</div>');
}

function add_new_info_message(msg)
{
  image_url = get_images_url();
  $("#content").prepend('<div class="info_msg"><img src="' + image_url +
      '/info.png"></img>' + msg + hide_box_html + '</div>');
}

function get_app_location()
{
  var v = window.location.href.split(/\index\.php\//);
  
  if(v.length != 2) {
    return '';
  }
  
  return v[1];
}

var base_url_cache = null;

function get_base_url()
{
  if(base_url_cache != null) {
    return base_url_cache;
  }
  
  var v = window.location.href.split(/\index\.php\//);
  
  base_url_cache = rtrim(v[0], '/');
  
  return base_url_cache;
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
  var val = $.cookie('paging-size');
  
  if(val) {
    return parseInt(val);
  } else {
    return 20; // default value
  }
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

function timestamp_to_date(timestamp)
{
  var date = timestamp.split(' ')[0];
  var parts = date.split('-');
  var year = parts[0];
  var month = parts[1];
  var day = parts[2];
  
  return day + '-' + month + '-' + year;
}

function urldecode(str)
{
  return decodeURIComponent(str);
}

function birthdayErrorPlacement(error, element)
{
  if(element.is("#birthday")) {
    error.appendTo(element.next().next());
  } else {
    error.appendTo(element.next());
  }
}

function basicErrorPlacement(error, element)
{
  error.appendTo(element.next());
}

function number_of_properties(obj)
{
  var count = 0;
  for (k in obj) if (obj.hasOwnProperty(k)) count++;
  return count;
}

$.blockLoadingUI = function () {
  return $.blockUI({ message: $('img#loading_image') });
};