var operator_select = null;
var operator_input = null;
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
var we_are_starting = true;

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
  if(type == 'bool') {
    data_input.hide();
    data_boolean_input.show();
    operator_input.hide();
  } else {
    operator_input.show();
    data_input.show();
    data_boolean_input.hide();
    operator_select.removeOption(/./);
    operator_select.addOption(fill_operators_options(type));
  }
}

function label_was_selected(row, grid)
{
  current_row = row;
  labelname.text(row.name);
  fill_operators(row.type);
  term_form.show();
}

function term_form_submitted()
{
  var type = current_row.type;
  var label = current_row.name;
  var obj = {label: label};

  if(type == 'bool') {
    obj.oper = 'eq';
    obj.value = data_boolean_checkbox.is(':checked');
  } else {
    obj.oper = operator_select.val();
    obj.value = data_row.val();
  }

  var li = get_li_selected();

  if(li.size() == 0) {
    return;
  }

  add_li_term(li, obj);

  if(!we_are_starting) {
    var ul = li.parent();
    add_radio_box(ul, true);
  }

  we_are_starting = false;
  update_search();
}

function and_form_submitted()
{
  handle_or_and('and');
}

function add_li_term(li, obj)
{
  var txt = obj.label + " " + obj.oper + " " + obj.value;

  li.html(txt);
  li[0].term = obj;
}

function add_new_andor(li_obj, txt)
{
  li_obj.html(txt + '<ul></ul>');
  li_obj[0].term = txt;
  return $('ul', li_obj);
}

function update_search()
{
  var obj = get_search_term(tree_form.children('ul:first').children('li:first'));
  if(obj) {
    var encoded = $.toJSON(obj);
    show_seqs.gridFilter('search', encoded);
    show_seqs.gridReload();
    alert(encoded);
  }
}

function handle_or_and(what)
{
  var obj = get_li_selected();

  if(obj.size() == 0) {
    return;
  }

  var new_ul = add_new_andor(obj, what);
  add_radio_box(new_ul, true);

  if(!we_are_starting) {
    var upper_ul = obj.parent();
    add_radio_box(upper_ul, false);
  }

  we_are_starting = false;
  //update_search();
}

function or_form_submitted()
{
  handle_or_and('or');
}

function get_li_selected()
{
  return $('input[name=new_term_radio]:checked').parent();
}

function add_radio_box(ul_dom, selected)
{
  var checked = selected ? 'checked' : '';
  ul_dom.append('<li><input type="radio" name="new_term_radio" ' +
        checked + '>Add here</input></li>');
}

function get_search_term(node)
{
  if(node == null || node.size() == 0) {
    return null;
  }

  var ul_child = node.children('ul');
  var is_terminal = ul_child.size() == 0;
  var term = node[0].term;

  if(is_terminal) {
    return term;
  } else {
    if(!term) {
      return null;
    }

    var li_children = ul_child.children('li');
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
    term_form.hide();

    add_radio_box($('ul:first', tree_form), true);

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
