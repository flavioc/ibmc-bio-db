var operator_select = null;
var operator_input = null;
var operator_text = null;
var term_form = null;
var term_form_div = null;
var and_form = null;
var or_form = null;
var current_label = null;
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
var not_form = null;
var insert_terms = null;
var data_tax = null;
var data_tax_input = null;
var change_tax = null;
var data_seq = null;
var data_seq_input = null;
var change_seq = null;
var data_position_input = null;
var position_type = null;
var position_type_text = null;
var submit_tree = null;
var we_are_starting = true;
var search_human = null;
var search_type = 'all';

$(function () {
  var got = $.getURLParam('type');

  if(got) {
    search_type = got;
  }
});

var term_options_html = '<span class="term-options" style="display: none;">(<span class="term-delete">x</span>) [<span class="term-count"></span>]</span>';

function get_cookie_tree_name()
{
  switch(search_type) {
    case 'dna':
      return 'saved_search_tree_dna';
    case 'protein':
      return 'saved_search_tree_protein';
    default:
      return 'saved_search_tree';
  }
}

function convert_operator(oper, type)
{
  if(type == 'integer' || type == 'position') {
    switch(oper) {
      case 'eq': return '=';
      case 'gt': return '>';
      case 'lt': return '<';
      case 'ge': return '>=';
      case 'le': return '<=';
    }
  } else if(type == 'text' || type == 'url') {
    switch(oper) {
      case 'eq': return 'equal';
      case 'contains': return 'contains';
      case 'starts': return 'starts';
      case 'ends': return 'ends';
      case 'regexp': return 'regexp';
    }
  }

  switch(oper) {
    case 'eq': return 'equal';
    case 'exists': return 'exists';
    case 'notexists': return 'not exists';
  }

  return '';
}

function fill_operators_options(type)
{
  var base = {exists: 'exists', notexists: 'not exists'};

  switch(type) {
    case 'position':
    case 'integer':
      return $.extend(base, {eq: '=',
                             gt: '>',
                             lt: '<',
                             ge: '>=',
                             le: '<='});
    case 'text':
    case 'url':
      return $.extend(base, {eq: 'equal',
                            contains: 'contains',
                            starts: 'starts',
                            ends: 'ends',
                            regexp: 'regexp'});
    default:
      return $.extend(base, {eq: 'equal'});
  }
}

function init_operator_select(type)
{
  operator_select.removeOption(/./);
  operator_select.addOption(fill_operators_options(type));
}

function fill_operators(type)
{
  data_input.hide();
  data_boolean_input.hide();
  data_tax_input.hide();
  data_seq_input.hide();
  data_position_input.hide();
  operator_text.hide();
  init_operator_select(type);
  operator_select.show();
  operator_input.show();

  show_type_input(type);
}

function show_type_input(type)
{
  switch(type) {
    case 'bool':
      data_boolean_input.show();
      break;
    case 'tax':
      data_tax_input.show();
      break;
    case 'ref':
      data_seq_input.show();
      break;
    case 'position':
      data_position_input.show();
      data_input.show();
      break;
    case 'integer':
      data_input.show();
      break;
    default:
      data_input.show();
  }
}

function hide_type_input(type)
{
  switch(type) {
    case 'bool':
      data_boolean_input.hide();
      break;
    case 'tax':
      data_tax_input.hide();
      break;
    case 'ref':
      data_seq_input.hide();
      break;
    case 'position':
      data_position_input.hide();
      data_input.hide();
      break;
    case 'integer':
      data_input.hide();
      break;
    default:
      data_input.hide();
  }
}

function term_form_submitted()
{
  if(!term_was_selected()) {
    alert("no selected");
    return;
  }

  if(current_label == null) {
    alert("no label");
    return;
  }

  var type = current_label.type;
  var label = current_label.name;
  var oper = operator_select.val();
  var obj = {label: label,
             type: type,
             id: current_label.id,
             oper: oper};

  if(oper != 'exists' && oper != 'notexists') {
    switch(type) {
      case 'bool':
        obj.value = data_boolean_checkbox.is(':checked');
        break;
      case 'tax':
        obj.value = data_tax[0].tax;
        if(obj.value == null) {
          return;
        }
        break;
      case 'ref':
        obj.value = data_seq[0].seq;
        if(obj.value == null) {
          return;
        }
        break;
      case 'position':
        data = {num: parseInt(data_row.val()),
              type: position_type.val()};
        obj.value = data;

        if(!is_numeric(data.num)) {
          return;
        }
        break;
      default:
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
  }

  var li = get_li_selected();

  if(li.size() == 0) {
    return;
  }

  var new_ol = add_li_term(li, obj);
  var old_li = li.parent().parent();

  handle_post_add(old_li, new_ol);
  update_search();
  
  // recompute selected compound term
  compute_total_term(old_li);

  return new_ol;
}

function and_form_submitted()
{
  handle_compound('and');
}

function not_form_submitted()
{
  handle_compound('not');
}

function build_operator_text(obj)
{
  if(obj.oper == 'exists' || obj.oper == 'notexists') {
    return convert_operator(obj.oper, obj.type);
  }

  switch(obj.type) {
    case 'bool':
      if(obj.value) {
        return 'is true';
      } else {
        return 'is false';
      }
      break;
    case 'tax':
      return 'is ' + obj.value.name;
    case 'ref':
      return 'is ' + obj.value.name;
    case 'position':
      return obj.value.type + ' ' + convert_operator(obj.oper, obj.type) + ' ' + obj.value.num;
    default:
      return convert_operator(obj.oper, obj.type) + ' ' + obj.value;
  }
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

function add_new_compound(li_obj, txt)
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

function enclose_search_tree(tree)
{
  switch(search_type) {
    case 'dna':
      return {oper: 'and',
        operands: [{label: 'type', type: 'text', oper: 'eq', value: 'dna'},
                    tree]};
    case 'protein':
      return {oper: 'and',
        operands: [
          {label: 'type', type: 'text', oper: 'eq', value: 'protein'},
          tree]};
    default:
      return tree;
  }
}

function get_simple_search_tree()
{
  return get_search_term(
      $('#search_tree ol:first').children('li:first'));
}

function get_main_search_term()
{
  return enclose_search_tree(get_simple_search_tree());
}

function update_form_hidden(encoded)
{
  $('input[name=encoded_tree]').val(encoded);
}

function get_main_search_term_encoded()
{
  return $.toJSON(get_main_search_term());
}

function update_humanize(encoded)
{
  if(!encoded) {
    encoded = get_main_search_term_encoded();
  }
  
  $.get(get_app_url() + '/sequence/humanize_search',
    {
      search: encoded
    },
    function (data) {
      search_human.html('<p>' + data + '</p>');
    });
}

function update_tree (encoded)
{
  if(!encoded) {
    encoded = get_main_search_term_encoded();
  }
  
  update_form_hidden(encoded);
  save_search_tree();
  
  update_humanize(encoded);
}

function reload_results(encoded)
{
  if(!encoded) {
   encoded = get_main_search_term_encoded(); 
  }
  
  show_seqs.gridFilter('search', encoded);
  show_seqs.gridReload();
}

function update_search()
{
  var encoded = get_main_search_term_encoded();
  
  update_tree(encoded);
  reload_results(encoded);
}

function handle_compound(what)
{
  if(!term_was_selected()) {
    return;
  }

  var obj = get_li_selected();

  if(obj.size() == 0) {
    return;
  }

  var new_ol = add_new_compound(obj, what);
  var old_li = obj.parent().parent();

  handle_post_add(old_li, new_ol);
  update_tree();

  return new_ol;
}

function handle_post_add(old_li, new_ol)
{
  if(old_li.size() == 1) {
    var li_this = old_li[0];
    var term = li_this.term;

    if(term == 'not') {
      cant_add_leafs();
    }
  }

  if(we_are_starting) {
    we_are_starting = false;
    cant_add_leafs();
  }
}

function or_form_submitted()
{
  handle_compound('or');
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
  current_label = null;
  label_name.hide();
  label_row.show();
  submit_term.hide();
}

function got_new_label(data)
{
  label_name.text(data.name).show();
  label_row.hide();
  fill_operators(data.type);
  term_other_fields.show();
  current_label = data;
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

function enable_position_type_select()
{
  position_type.show();
  position_type_text.hide();
}

function get_operator_text() 
{
  var selected = $('option:selected', operator_select);
  return selected.text();
}

function select_position_type()
{
  if(current_label.type == 'position') {
    position_type.hide();
    position_type_text.text(position_type.val()).show();
  }
}

function operator_was_selected()
{
  var selected_text = get_operator_text();

  operator_select.hide();
  operator_text.text(selected_text).show();

  var op = operator_select.val();

  if(op == 'exists' || op == 'notexists') {
    hide_type_input(current_label.type);
  } else {
    show_type_input();
    select_position_type();
  }
}

function node_unselected()
{
  $('#search_tree *').removeClass('selected-node');
  $('#search_tree .term-options').hide();
}

function can_add_leafs()
{
  $('input, select', insert_terms).removeAttr('disabled');
  $('#change_tax, #change_seq', insert_terms).show();
}

function cant_add_leafs()
{
  $('input, select', insert_terms).attr('disabled', 'true');
  $('#change_tax, #change_seq', insert_terms).hide();
}

function compute_total_term(li)
{
  var search = enclose_search_tree(get_search_term(li));
  var encoded = $.toJSON(search);
  
  $.get(get_app_url() + '/sequence/get_search_total',
    {
      search: encoded
    },
    function (data) {
      $('.term-count:first', li).text(data);
    });
}

function activate_term(name)
{
  var parent = $(name).parent();
  var options = $('.term-options:first', parent);
  parent.addClass('selected-node');
  options.show();

  compute_total_term(parent);
}

function save_search_tree()
{
  var options = {path: '/',
    expires: 10
  };

  var obj = get_simple_search_tree();
  var encoded = $.toJSON(obj);

  $.cookie(get_cookie_tree_name(), encoded);
}

function get_start_search_param()
{
  return $.toJSON(get_main_search_term());
}

function restore_old_tree()
{
  var encoded = $.cookie(get_cookie_tree_name());

  if(!encoded) {
    return;
  }

  var obj = $.evalJSON(encoded);

  if(obj) {
    var first_ol = $('#search_tree ol:first');

    cant_add_leafs();
    restore_aux(obj, first_ol);
    we_are_starting = false;

    update_form_hidden(encoded);
    update_humanize(encoded);
  } else {
    can_add_leafs();
  }
}

function compound_term(oper)
{
  return oper == 'or' || oper == 'and' || oper == 'not';
}

function restore_aux(obj, ol)
{
  var oper = obj.oper;
  var new_li = $('<li></li>');

  new_li.appendTo(ol);

  if(compound_term(oper)) {
    var new_ol = add_new_compound(new_li, oper);
    var operands = obj.operands;

    $.each(operands, function () {
        restore_aux(this, new_ol);
    });
  } else {
    add_li_term(new_li, obj);
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
    labelname = $('#labelname');
    tree_form = $('#tree_form');
    label_row = $('#label_row');
    label_name = $('#label_name');
    operator_text = $('#operator_text');
    term_other_fields = $('#term_other_fields');
    and_form = $('#and_form');
    or_form = $('#or_form');
    not_form = $('#not_form');
    insert_terms = $('#insert_terms');
    data_tax = $('#data_tax');
    change_tax = $('#change_tax');
    data_tax_input = $('#data_tax_input');
    data_seq = $('#data_seq');
    change_seq = $('#change_seq');
    data_seq_input = $('#data_seq_input');
    data_position_input = $('#data_position_input');
    position_type = $('#position_type');
    position_type_text = $('#position_type_text');
    submit_tree = $('#submit_tree');
    submit_term = $('#submit_term').hide();
    search_human = $('#search_human');

    can_add_leafs();

    data_row.focus(operator_was_selected);

    tree_form.submit(function () {
        return true;
    });

    change_tax.click(function () {
        var url = get_app_url() + '/sequence/search_tax';
        tb_show('Find taxonomy', url);
        return false;
    });

    change_seq.click(function () {
        var url = get_app_url() + '/sequence/search_ref';
        tb_show('Find sequence', url);
        return false;
    });

    $('.term-delete').livequery('click', function () {
        var li_term = $(this).parent().parent();
        var obj = li_term[0].term;

        li_term.remove();
        
        if(compound_term(obj) && $('.term-name', li_term).size() == 0) {
          // no terms in this subtree
          update_tree();
        } else {
          update_search();
        }

        if($('#search_tree li').size() == 0) {
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
        var what = $(this).text();

        node_unselected();

        if(what == 'not') {
          if($('li', $(this).parent()).size() == 0) {
            can_add_leafs();
          } else {
            cant_add_leafs();
          }
        } else {
          can_add_leafs();
        }

        activate_term(this);
    });

    $('#search_tree .term-name').livequery('click', function () {
        node_unselected();
        cant_add_leafs();
        activate_term(this);
    });

    label_name.click(enable_label_row);

    position_type_text.click(enable_position_type_select);
    position_type.change(select_position_type);

    operator_text.click(enable_operator_select);

    operator_select.change(operator_was_selected);

    label_row.autocomplete_labels();

    label_row.autocompleteEmpty(function () {
        hide_term();
    });

    label_row.result(function (event, data, formatted) {
        var name = data;

        if(!name) {
          hide_term();
          return;
        }

        get_label_by_name(name, 
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
      submitHandler: term_form_submitted
    });

    and_form.validate({
      submitHandler: and_form_submitted
    });

    or_form.validate({
      submitHandler: or_form_submitted
    });

    not_form.validate({
      submitHandler: not_form_submitted
    });

  restore_old_tree();

  show_seqs
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_search',
    total: 'get_search_total',
    params: {
      search: get_start_search_param()
    },
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
