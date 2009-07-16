var current_label = null;
var hidden_search = null;
var multiple = null;
var update = null;

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
  $('input[name=update]', form).val(update.is(":checked"));
  $('input[name=multiple]', form).val(multiple.is(":checked"));
  $('input[name=label_id]', form).val(current_label.id);
}

$(function () {
  var label_form = $('#label_form');
  var label_input = $('input[name=label]', label_form);
  var selected_label = $('#selected_label');

  update = $('#update');
  multiple = $('#multiple');

  var multiple_row = multiple.parent();

  hidden_search = $('input[name=search]', label_form);

  $('#form_add_label').livequery(function () {
    $(this).submitAjax();
  });

  function no_label_present()
  {
    current_label = null;
    selected_label.text('(no label selected)');
    multiple_row.hide();
  }

  function changed_label(l)
  {
    current_label = l;
    selected_label.hide();
    if(l.multiple == '1') {
      multiple_row.show();
    }
  }

  no_label_present();

  label_input.autocomplete_labels('autocomplete_addable');

  label_input.autocompleteEmpty(no_label_present);

  label_input.result(function (event, data, formatted) {
        no_label_present();

        var name = data;

        if(!name) {
          return;
        }

        get_label_by_name(name, 
          function (data) {
            if(data) {
              changed_label(data);
            }
        });

        return false;
    });

  $('#submit_add_label').click(function () {
    if(!current_label) {
      return false;
    }

    tb_show('Add label', get_app_url() + '/multiple_labels/add_dialog/' + current_label.id);

    return false;
  });
});
