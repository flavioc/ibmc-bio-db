var operator_select = null;
var operator_input = null;
var operator_text = null;
var term_form = null;
var term_form_div = null;
var and_form = null;
var or_form = null;
var current_row = null;
var data_input = null;
var data_row = null;
var data_boolean_input = null;
var data_boolean_checkbox = null;
var show_seqs = null;
var tree_form = null;
var labelname = null;
var label_row = null;
var label_name = null;
var term_other_fields = null;
var submit_term = null;
var or_form = null;
var and_form = null;
var insert_terms = null;
var data_tax = null;
var data_tax_input = null;
var change_tax = null;
var we_are_starting = true;
var can_add_expanders = true;

var term_options_html = '<span class="term-options" style="display: none;">(<span class="term-delete">x</span>)</span>';

function fill_operators_options(type)
{
  switch(type) {
    case 'integer':
      return {eq: '=',
              gt: '>',
              lt: '<',
              ge: '>=',
              le: '<='};
    case 'text':
    case 'url':
      return {eq: 'Equal',
              contains: 'Contains',
              starts: 'Starts',
              ends: 'Ends'};
  }

  return {};
}

function fill_operators(type)
{
  operator_input.hide();
  data_input.hide();
  data_boolean_input.hide();
  data_tax_input.hide();
  change_tax.hide();
  data_tax.hide();

  if(type == 'bool') {
    data_boolean_input.show();
  } else if(type == 'tax') {
    data_tax_input.show();
    change_tax.show();
    data_tax.show();
  } else {
    operator_input.show();
    data_input.show();
    operator_select.show();
    operator_select.removeOption(/./);
    operator_select.addOption(fill_operators_options(type));
  }
}

function term_form_submitted()
{
  if(!term_was_selected()) {
    return;
  }

  var type = current_row.type;
  var label = current_row.name;
  var obj = {label: label, type: type};

  if(type == 'bool') {
    obj.oper = 'eq';
    obj.value = data_boolean_checkbox.is(':checked');
  } else if(type == 'tax') {
    obj.oper = 'eq';
    obj.value = data_tax[0].tax;
    if(obj.value == null) {
      return;
    }
    alert(obj.value.id);
  } else {
    obj.oper = operator_select.val();
    obj.value = data_row.val();

    if(obj.type == 'integer') {
      obj.value = parseInt(obj.value);
      if(!is_numeric(obj.value)) {
        return;
      }
    } else {
      // text based value
      if(obj.value == '') {
        return;
      }
    }
  }

  var li = get_li_selected();

  if(li.size() == 0) {
    return;
  }

  add_li_term(li, obj);

  if(!we_are_starting) {
    var ol = li.parent();
  }

  if(we_are_starting) {
    we_are_starting = false;
    can_add_expanders = false;
    cant_add_leafs();
  }
  update_search();
}

function and_form_submitted()
{
  handle_or_and('and');
}

function build_operator_text(obj)
{
  if(obj.type == 'bool') {
    if(obj.value) {
      return 'is true';
    } else {
      return 'is false';
    }
  } else if(obj.type == 'tax') {
    return 'is ' + obj.value.name;
  }

  return get_operator_text() + ' ' + obj.value;
}

function add_li_term(li, obj)
{
  var txt = obj.label + " " + build_operator_text(obj);
  var level = li.parent().attr("level");

  li.attr("level", level);
  li.addClass("search-term");
  li.html('<span class="term-name">' + txt + '</span>' + term_options_html);
  li[0].term = obj;
}

function add_new_andor(li_obj, txt)
{
  var ol = $('<ol class="search-list"></ol>');
  var li_parent = li_obj.parent();
  var level = li_parent.attr("level");
  li_obj.attr("level", level);
  ol.attr("level", (1 + parseInt(level)).toString());

  li_obj.html('<span class="expand-name">' + txt + '</span>' + term_options_html);

  ol.appendTo(li_obj);
  li_obj[0].term = txt;
  li_obj.addClass("search-expand");

  return $('ol', li_obj);
}

function update_search()
{
  var obj = get_search_term(tree_form.children('ol:first').children('li:first'));
  if(obj) {
    var encoded = $.toJSON(obj);
    show_seqs.gridFilter('search', encoded);
    show_seqs.gridReload();
    alert(encoded);
  }
}

function handle_or_and(what)
{
  if(!term_was_selected()) {
    return;
  }

  var obj = get_li_selected();

  if(obj.size() == 0) {
    return;
  }

  var new_ol = add_new_andor(obj, what);

  if(!we_are_starting) {
    var upper_ol = obj.parent();
  }

  if(we_are_starting) {
    we_are_starting = false;
    cant_add_leafs();
  }
}

function or_form_submitted()
{
  handle_or_and('or');
}

function term_was_selected()
{
  return we_are_starting ||
    $('#search_tree .selected-node > ol').size() == 1;
}

function get_li_selected()
{
  var selected_list = $('.selected-node > ol');
  var new_li = $('<li></li>');

  if(selected_list.size() == 0) {
    selected_list = $('#search_tree ol:first');
  }

  new_li.appendTo(selected_list);

  return new_li;
}

function get_search_term(node)
{
  if(node == null || node.size() == 0) {
    return null;
  }

  var ol_child = node.children('ol');
  var is_terminal = ol_child.size() == 0;
  var term = node[0].term;

  if(is_terminal) {
    return term;
  } else {
    if(!term) {
      return null;
    }

    var li_children = ol_child.children('li');
    var obj = {oper: term, operands: []};

    $.each(li_children, function (index, li) {
        var ret = get_search_term($(li));
        if(ret) {
          obj.operands.push(ret);
        }
    });

    return obj;
  }
}

function hide_term() {
  term_other_fields.hide();
  current_row = null;
  label_name.hide();
  label_row.show();
  submit_term.hide();
}

function got_new_label(data) {
  label_name.text(data.name).show();
  label_row.hide();
  fill_operators(data.type);
  term_other_fields.show();
  operator_text.hide();
  current_row = data;
  submit_term.show();
}

function enable_label_row()
{
  label_name.hide();
  label_row.show();
}

function enable_operator_select()
{
  operator_select.show();
  operator_text.hide();
}

function get_operator_text() 
{
  var selected = $('option:selected', operator_select);
  return selected.text();
}

function operator_was_selected()
{
  var selected_text = get_operator_text();

  operator_select.hide();
  operator_text.text(selected_text).show();
}

function node_unselected()
{
  $('#search_tree *').removeClass('selected-node');
  $('#search_tree .term-options').hide();
}

function can_add_leafs()
{
  $('input, select', insert_terms).removeAttr('disabled');
}

function cant_add_leafs()
{
  $('input, select', insert_terms).attr('disabled', 'true');
}

function activate_term(name)
{
  var parent = $(name).parent();
  var options = $('.term-options:first', parent);
  parent.addClass('selected-node');
  options.show();
}

$(document).ready(function () {
    
    operator_select = $('#operator');
    operator_input = $('#operator_input');
    term_form = $('#term_form');
    term_form_div = $('#term_form_div');
    data_input = $('#data_input');
    data_boolean_input = $('#data_boolean_input');
    data_boolean_checkbox = $('#data_boolean_checkbox');
    data_boolean_checkbox = $('#data_boolean_checkbox');
    data_row = $('#data_row');
    show_seqs = $('#show_sequences');
    and_form = $('#and_form');
    or_form = $('#or_form');
    labelname = $('#labelname');
    tree_form = $('#tree_form');
    label_row = $('#label_row');
    label_name = $('#label_name');
    operator_text = $('#operator_text');
    term_other_fields = $('#term_other_fields');
    and_form = $('#and_form');
    or_form = $('#or_form');
    insert_terms = $('#insert_terms');
    data_tax = $('#data_tax');
    change_tax = $('#change_tax');
    data_tax_input = $('#data_tax_input');
    submit_term = $('#submit_term').hide();

    can_add_leafs();

    data_row.focus(operator_was_selected);

    change_tax.click(function () {
        var url = get_app_url() + '/sequence/search_tax';
        tb_show('Find taxonomy', url);
        return false;
    });

    $('.term-delete').livequery('click', function () {
        var li_term = $(this).parent().parent();

        li_term.remove();
        update_search();

        if($('#search_tree li').size() == 0) {
          can_add_expanders = true;
          we_are_starting = true;
          can_add_leafs();
        } else {
          cant_add_leafs();
        }

        return false;
    });

    $('#search_tree .expand-name, #search_tree .term-name').livequery(function () {
        $(this).hover(function () {
          $(this).addClass("selected-term");
          return false;
        },
        function () {
          $(this).removeClass('selected-term');
          return false;
        });
    });

    $('#search_tree .expand-name').livequery('click', function () {
        node_unselected();
        can_add_leafs();
        activate_term(this);
    });

    $('#search_tree .term-name').livequery('click', function () {
        node_unselected();
        cant_add_leafs();
        activate_term(this);
    });

    label_name.click(enable_label_row);

    operator_text.click(enable_operator_select);

    operator_select.change(operator_was_selected);

    label_row.autocomplete(get_app_url() + "/label/autocomplete_labels",
      {
        minChars: 0,
        delay: 400,
        scroll: true,
        selectFirst: false,
        mustMatch: true
      });

    label_row.autocompleteEmpty(function () {
        hide_term();
    });

    label_row.result(function (event, data, formatted) {
        var name = data;

        if(!name) {
          hide_term();
          return;
        }

        $.getJSON(get_app_url() + "/label/get_label_by_name/" + name,
          function (data) {
            if(data == null) {
              hide_term();
            } else {
              got_new_label(data);
            }
        });

        return false;
    });

    term_form.validate({
      submitHandler: term_form_submitted,
      errorPlacement: basicErrorPlacement,
      rules: {
        data_row: {
          required: function () {
            return current_row != null &&
                  (current_row.type == 'integer' || current_row.type == 'text');
          }
        }
      }
    });

    and_form.validate({
      submitHandler: and_form_submitted
    });

    or_form.validate({
      submitHandler: or_form_submitted
    });

  show_seqs
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_search',
    total: 'get_search_total',
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    tdClass: {user_name: 'centered', update: 'centered'},
    width: {
      user_name: w_user,
      update: w_update
    },
    ordering: {
      name: 'asc',
      update: 'def',
      user_name: 'def'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    }
  });

});
