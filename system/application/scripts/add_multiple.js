var multiple = null;
var update = null;
var addnew = null;
var mode = 'add';
var current_label = null;
var hidden_search = null;

$(function () {
  var got = $.getURLParam('mode');

  if(got) {
    mode = got;
  }
});

function get_search_tree_encoded()
{
  return hidden_search.val();
}

$.fn.submitAjax = function () {
  return this.ajaxForm({
    success: function (resp) { eval(resp); tb_remove(); },
    beforeSubmit: function (data, form) {
      return $(form).valid();
    }
  });
};

function update_new_label_form()
{
  var form = $('#form_add_label');

  $('input[name=search]', form).val(get_search_tree_encoded());
  
  if(mode == 'add') {
    $('input[name=update]', form).val(update.is(":checked"));
  } else if(mode == 'edit') {
    $('input[name=addnew]', form).val(addnew.is(":checked"));
  }
  
  $('input[name=mode]', form).val(mode);
  $('input[name=multiple]', form).val(multiple.is(":checked"));
  $('input[name=label_id]', form).val(current_label.id);
}

$(function () {
  var label_form = $('#label_form');
  var label_input = $('input[name=label]', label_form);

  update = $('#update');
  multiple = $('#multiple');
  addnew = $('#addnew');

  var multiple_row = multiple.parent();

  hidden_search = $('input[name=search]', label_form);

  $('#form_add_label').livequery(function () {
    $(this).submitAjax();
    update_new_label_form();
  });
  
  label_input.selectLabel(function () {
    current_label = null;
    multiple_row.hide();
  },
  function (label) {
    current_label = label;
    if(label.multiple == '1') {
      multiple_row.show();
    }
  })

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