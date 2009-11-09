var update = null;
var addnew = null;
var mode = 'add';
var current_label = null;
var hidden_search = null;
var hidden_transform = null;
var loading = null;
var tag = "timer_label";
var event = null;

$(function () {
  var got = $.getURLParam('mode');

  if(got) {
    mode = got;
  }
});

function update_loading()
{
  var url = get_app_url() + "/event/get_label_status/"+event;
  $.get(url, {}, function (data) {
    $('#loading-labels div').html(data);
  }, 'text');
}

function get_search_tree_encoded()
{
  return hidden_search.val();
}

function get_transform()
{
  return hidden_transform.val();
}

$.fn.submitAjax = function () {
  return this.ajaxForm({
    success: function (resp) {
      loading.stopTime(tag, update_loading);
      $.unblockUI();
      eval(resp);
      tb_remove();
    },
    beforeSubmit: function (data, form) {
      if(!$(form).valid()) {
        return false;
      }
      
      event = $('input[name=event]').val();

      $('#loading-labels div').empty();
      
      $.blockUI({ message: loading,  css: {
          color:		'#000',
          border:		'3px solid #aaa',
          backgroundColor:'#fff'
        }
      });

      loading.stopTime(tag, update_loading).everyTime(2000, tag, update_loading);
      
      return true;
    }
  });
};

function update_new_label_form()
{
  var form = $('#form_add_label');

  $('input[name=search]', form).val(get_search_tree_encoded());
  $('input[name=transform]', form).val(get_transform());
  
  if(mode == 'add') {
    $('input[name=update]', form).val(update.is(":checked"));
  } else if(mode == 'edit') {
    $('input[name=addnew]', form).val(addnew.is(":checked"));
  }
  
  $('input[name=mode]', form).val(mode);
  $('input[name=label_id]', form).val(current_label.id);
}

$(function () {
  var label_form = $('#label_form');
  var label_input = $('input[name=label]', label_form);

  update = $('#update');
  addnew = $('#addnew');
  loading = $('#loading-labels');

  hidden_transform = $('input[name=transform]', label_form);
  hidden_search = $('input[name=search]', label_form);

  $('#form_add_label').livequery(function () {
    $(this).submitAjax();
    update_new_label_form();
  });
  
  $('#date').livequery(function () {
    $(this).datePickerDate();
  });
  
  label_input.selectLabel(function () {
    current_label = null;
  },
  function (label) {
    current_label = label;
  },
  'addable');

  $('#submit_add_label').click(function () {
    if(!current_label) {
      return false;
    }

    var url = get_app_url() + '/multiple_labels/add_dialog/' + current_label.id + '?mode=' + mode;
    
    if(mode == 'add') {
      tb_show('Add label', url);
    } else if(mode == 'edit') {
      tb_show('Edit label', url);
    }

    return false;
  });
});