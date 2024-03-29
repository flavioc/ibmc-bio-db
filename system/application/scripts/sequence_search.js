var DEBUG_SQL = false;
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
var search_sql = null;
var data_date_input = null;
var date_input = null;
var select_transform = null;
var current_selected_li = null;
var param_input = null;
var data_param = null;
var histogram_label = null;
var histogram_button = null;
var generate_histogram_type = null;
var label_result = null;
var label_result_info = null;
var add_label_button = null;
var show_result_list = null;
var show_result_total = null;
var view_full = null;
var shown_labels = [];
var search_type = 'all';
var use_result_list = false;

$(function () {
  var got = $.getURLParam('type');

  if(got) {
    search_type = got;
  }
});

//var term_options_html = '<span class="term-options" style="display: none;"> <span class="term-delete small-button">Delete</span> [<span class="term-count"></span>]</span>';
var term_options_html = '<span class="term-options" style="display: none;"> <span class="term-delete small-button">Delete</span></span>';

function get_cookie_tree_name()
{
  switch(search_type) {
    default:
      return 'saved_search_tree';
  }
}

function convert_operator(oper, type)
{
  switch(type) {
    case 'integer':
    case 'float':
    case 'position':
     switch(oper) {
        case 'eq': return '=';
        case 'gt': return '>';
        case 'lt': return '<';
        case 'ge': return '>=';
        case 'le': return '<=';
      }
      break;
    case 'text':
    case 'url':
    case 'obj':
      switch(oper) {
        case 'eq': return 'equal';
        case 'contains': return 'contains';
        case 'starts': return 'starts';
        case 'ends': return 'ends';
        case 'regexp': return 'regexp';
      }
      break;
    case 'date':
      switch(oper) {
        case 'before': return 'before';
        case 'after': return 'after';
      }
      break;
    case 'tax':
    case 'ref':
      return oper;
  }

  switch(oper) {
    case 'eq': return 'equal';
    case 'exists': return 'exists';
    case 'notexists': return 'not exists';
  }

  return oper;
}

function fill_operators_options(type)
{
  var base = {exists: 'exists', notexists: 'not exists'};

  switch(type) {
    case 'position':
    case 'integer':
    case 'float':
      return $.extend(base, {eq: '=',
                             gt: '>',
                             lt: '<',
                             ge: '>=',
                             le: '<='});
    case 'text':
    case 'url':
    case 'obj':
      return $.extend(base, {eq: 'equal',
                            contains: 'contains',
                            starts: 'starts',
                            ends: 'ends',
                            regexp: 'regexp'});
    case 'date':
      return $.extend(base, {eq: 'equal',
                             after: 'after',
                             before: 'before'});
    case 'tax':
    case 'ref':
      return $.extend(base, {eq: 'equal',
                              like: 'like'});
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
  init_operator_select(type);
  operator_select.show();
  operator_input.show();
}

function show_type_input(type, oper)
{
  if(oper == 'exists' || oper == 'notexists') {
    return;
  }
  
  switch(type) {
    case 'bool':
      data_boolean_input.show();
      break;
    case 'position':
      data_position_input.show();
      data_input.show();
      break;
    case 'integer':
    case 'float':
      data_input.show();
      break;
    case 'date':
      data_date_input.show();
      break;
    case 'tax':
      if(oper == 'like' || oper == null) {
        data_tax_input.hide();
        data_input.show();
      } else if(oper == 'eq') {
        data_input.hide();
        data_tax_input.show();
      }
      break;
    case 'ref':
      if(oper == 'like' || oper == null) {
        data_seq_input.hide();
        data_input.show();
      } else if(oper == 'eq') {
        data_seq_input.show();
        data_input.hide();
      }
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
      data_input.hide();
      break;
    case 'ref':
      data_seq_input.hide();
      data_input.hide();
      break;
    case 'position':
      data_position_input.hide();
      data_input.hide();
      break;
    case 'integer':
    case 'float':
      data_input.hide();
      break;
    case 'date':
      data_date_input.hide();
      break;
    default:
      data_input.hide();
  }
}

function term_form_submitted()
{
  if(!term_was_selected()) {
    return;
  }

  if(current_label == null) {
    return;
  }

  var type = current_label.type;
  var label = current_label.name;
  var oper = operator_select.val();
  
  
  
  var obj = {label: label,
             type: type,
             oper: oper};
             
  if(current_label.multiple == '1') {
    var param = data_param.val();
    if(param == '') {
      param = null;
    }
    
    obj.param = param;
  }

  if(oper != 'exists' && oper != 'notexists') {
    switch(type) {
      case 'bool':
        obj.value = data_boolean_checkbox.is(':checked');
        break;
      case 'tax':
        if(oper == 'eq') {
          var tax = data_tax[0].tax;
        
          if(tax == null) {
            return;
          }
        
          obj.value = {id: tax.id, name: tax.name};
        } else if(oper == 'like') {
          obj.value = data_row.val();
          
          if(!non_empty_string(obj.value)) {
            return;
          }
        } else {
          return;
        }
        break;
      case 'ref':
        if(oper == 'eq') {
          obj.value = data_seq[0].seq;
          
          if(obj.value == null) {
            return;
          }
        } else if(oper == 'like') {
          obj.value = data_row.val();
          
          if(!non_empty_string(obj.value)) {
            return;
          }
        } else {
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
      case 'date':
        var val = date_input.val();
        if(!val) {
          return;
        }
        obj.value = val;
        break;
      case 'integer':
        obj.value = parseInt(data_row.val());
        
        if(!is_numeric(obj.value)) {
          return;
        }
        break;
      case 'float':
        obj.value = parseFloat(data_row.val());
        
        if(!is_numeric(obj.value)) {
          return;
        }
        break;
      default: // text, url, obj
        obj.value = data_row.val();

        // other values
        if(!non_empty_string(obj.value)) {
          return;
        }
    }
  }

  var li = get_li_selected();

  if(li.size() == 0) {
    return;
  }

  var new_ol = add_li_term(li, obj);
  var old_li = li.parents('li:first');

  handle_post_add(old_li, new_ol);
  update_search();
  
  if(old_li.size() == 1) {
    // recompute selected compound term
    compute_total_term(old_li);
  } else {
    // select single term
    $('.term-name', li).clickTermName();
  }

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

function reset_form_submitted()
{
  $('#search_tree .term-delete:first').click();
  and_form_submitted();
  
  return false;
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
      if(obj.oper == 'eq') {
        return 'is ' + obj.value.name;
      } else if(obj.oper == 'like') {
        return 'like ' + obj.value;
      }
    case 'ref':
      if(obj.oper == 'eq') {
        return 'is ' + obj.value.name;
      } else if(obj.oper == 'like') {
        return 'like ' + obj.value;
      }
    case 'position':
      return obj.value.type + ' ' + convert_operator(obj.oper, obj.type) + ' ' + obj.value.num;
    default:
      return convert_operator(obj.oper, obj.type) + ' ' + obj.value;
  }
}

function add_li_term(li, obj)
{
  var txt = obj.label + (obj.param ? ("[" + obj.param + "]") : '') + " " + build_operator_text(obj);
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

  li_obj.html('<span class="expand-name">' + txt.toUpperCase() + '</span>' + term_options_html);

  ol.appendTo(li_obj);
  li_obj[0].term = txt;
  li_obj.addClass("search-expand");

  return $('ol', li_obj);
}

function enclose_search_tree(tree)
{
  switch(search_type) {
    case 'dna':
    case 'protein':
      var base = {label: 'type', type: 'text', oper: 'eq', value: search_type};
      
      if(tree) {
        return {oper: 'and', operands: [base, tree]};
      } else {
        return {oper: 'and', operands: [base]};
      }
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
  $('input[name=transform_hidden]').val(select_transform.val());
  $('input[name=type_hidden]').val(search_type);
}

function get_main_search_term_encoded()
{
  return $.toJSON(get_main_search_term(), true);
}

function update_humanize(encoded)
{
  if(!encoded) {
    encoded = get_main_search_term_encoded();
  }
  
  $.post(get_app_url() + '/search/humanize',
    {
      search: encoded
    },
    function (data) {
      search_human.html('<p>' + data + '</p>');
    });
    
  update_sql(encoded);
}

function update_sql(encoded)
{
  // also update sql
  if(DEBUG_SQL) {
     $.post(get_app_url() + '/search/sql',
     {
        search: encoded,
        transform: select_transform.val()
     },
     function (data) {
        search_sql.html('<p>' + data + '</p>');
        search_sql.show();
     });
 } else {
    search_sql.hide();
 }
}

function update_tree(encoded)
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
  
  if(use_result_list) {
     show_seqs.gridFilter('search', encoded);
     show_seqs.gridFilter('transform', select_transform.val());
     show_seqs.gridReload();
  } else {
     // only get total
     var loader = $('#total_loader', show_result_total);
     var num = $('#total_num', show_result_total);
     
     loader.show();
     num.hide();
     view_full.hide();
     
     $.post(get_app_url() + '/search/get_search_total',
         {
           search: encoded,
           transform: select_transform.val()
         },
         function (data) {
            loader.hide();
            view_full.show();
            num.html('Found <strong>' + data + '</strong> sequences').show();
         });
  }
}

// reload results and search string
function update_search()
{
  var encoded = get_main_search_term_encoded();
  
  update_tree(encoded);
  reload_results(encoded);
}

// upload screen based on new transform
function update_transform()
{
  var encoded = get_main_search_term_encoded();
  
  reload_results(encoded);
  update_form_hidden(encoded);
  if(current_selected_li) {
    compute_total_term(current_selected_li);
  }
  
  update_sql(encoded);
}

function handle_compound(what)
{
  if(!term_was_selected()) {
    return;
  }

  var li = get_li_selected();

  if(li.size() == 0) {
    return;
  }

  var new_ol = add_new_compound(li, what);
  var old_li = li.parent().parent();

  handle_post_add(old_li, new_ol);
  update_tree();
  
  $('.expand-name', li).clickCompoundName();

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
  current_label = data;
  
  data_param.val('');
  
  if(current_label.multiple == '1') {
    param_input.show();
  } else {
    param_input.hide();
  }
  
  label_name.text(data.name).show();
  label_row.hide();
  data_input.hide();
  data_boolean_input.hide();
  data_tax_input.hide();
  data_seq_input.hide();
  data_position_input.hide();
  operator_text.hide();
  data_date_input.hide();
  
  fill_operators(data.type);
  show_type_input(data.type);
  term_other_fields.show();
  
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
    show_type_input(current_label.type, op);
    select_position_type();
  }
}

function node_unselected()
{
  $('#search_tree *').removeClass('selected-node');
  $('#search_tree .term-options').hide();
}

function get_term_input_elements()
{
  var parent = $('#term_form_div, #and_form, #or_form, #not_form');
  
  return $('input, select', parent);
}

function can_add_leafs()
{
  get_term_input_elements().removeAttr('disabled');
  $('#change_tax, #change_seq', insert_terms).show();
}

function cant_add_leafs()
{
  get_term_input_elements().attr("disabled", true);
  $('#change_tax, #change_seq', insert_terms).hide();
}

function compute_total_term(li)
{
  var search = enclose_search_tree(get_search_term(li));
  var encoded = $.toJSON(search, true);
  
  current_selected_li = li;
  
  /*
  $.post(get_app_url() + '/search/get_search_total',
    {
      search: encoded,
      transform: select_transform.val()
    },
    function (data) {
      $('.term-count:first', li).text(data).effect('highlight', {}, 500);
    });*/
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
  var obj = get_simple_search_tree();
  var encoded = $.toJSON(obj, true);

  $.cookie(get_cookie_tree_name(), encoded, cookie_options);
}

function get_start_search_param()
{
  return $.toJSON(get_main_search_term(), true);
}

function restore_old_tree()
{
  var encoded = null;
  var obj = null;
  
  encoded = $.cookie(get_cookie_tree_name());
  obj = $.evalJSON(encoded);

  if(obj) {
    var first_ol = $('#search_tree ol:first');

    cant_add_leafs();
    restore_aux(obj, first_ol);
    we_are_starting = false;
    
    var enclosed = $.toJSON(enclose_search_tree(obj));

    update_form_hidden(enclosed);
    update_humanize(enclosed);
    
    // select the first term
    var first_compound = $('#search_tree .expand-name:first');
    if(first_compound.size() == 1) {
      first_compound.clickCompoundName();
    } else {
      $('#search_tree .term-name:first').clickTermName();
    }
    
    // refresh cookie
    save_search_tree();
  }
  
  if(we_are_starting) {
    and_form_submitted();
  }
  
  // load result total
  reload_results();
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

function no_histogram_label()
{
  histogram_label = null;
  generate_histogram_type.hide();
  histogram_button.attr('disabled', true);
}

function new_histogram_label(label)
{
  histogram_button.removeAttr('disabled');
  histogram_label = label;
  $('input[name=histogram_label]').val(label.id);
  if(label.multiple == '1' && (label.type == 'integer' || label.type == 'float'))
    generate_histogram_type.show();
  else
    generate_histogram_type.hide();
}

function no_label_result()
{
  label_result_info = null;
  add_label_button.attr('disabled', true);
}

function build_label_filter()
{
  var filter = '';
  
  $.each(shown_labels, function (i, l) {
    if(filter.length > 0)
      filter += '|';
    
    filter += l;
  });
  
  return filter;
}

function new_label_result(label)
{
  label_result_info = label;
  add_label_button.removeAttr('disabled');
}

function push_new_column_label()
{
  var label = label_result_info;

  if(!label)
    return;

  if(show_seqs.gridHasColumn(label.name))
    return false;
  
  shown_labels.push(label.name);
  
  show_seqs.gridFilter('labels', build_label_filter());
  show_seqs.gridAddColumn(label.name, label.name,
    function (row) {
      var data = row[label.name];
      
      return label_data_transform(data, label.type, label.name);
    });
    
  show_seqs.gridColumnSetDeletable(label.name, function () {
    
    shown_labels = $.grep(shown_labels, function (l) {
      return l != label.name;
    });
    
    show_seqs.gridFilter('labels', build_label_filter());
  });
  
  show_seqs.gridReload();
}

function get_label_field_data(data, type, name)
{
  var MAX_LABEL_TEXT = 50;
  
  switch(type) {
    case 'ref':
      return '<a href="' + get_app_url() + '/sequence/labels/' + data.id + '" target="_blank">' + data.string + '</a>';
    case 'tax':
      return '<a href="' + get_app_url() + '/taxonomy/view/' + data.id + '" target="_blank">' + data.string + '</a>';
    case 'obj':
      return '<a href="' + get_app_url() + '/file/get/' + data.id + '" target="_blank">' + data.string + '</a>';
    case 'url':
      return '<a href="' + data.string + '" target="_blank">' + data.string + '</a>';
    case 'text':
      if(name == 'creation_user' || name == 'update_user')
        return '<a href="' + get_app_url() + '/profile/view/' + data.id + '" target="_blank">' + data.string + '</a>';
      
      if(data.string.length > MAX_LABEL_TEXT)
        return split_string_html(data.string, MAX_LABEL_TEXT);
      else
        return data.string;
    default:
      return data.string;
  }
}

function label_data_transform(label_data, type, name)
{
  if(!label_data)
    return null;
    
  var ret = '';
  
  if(typeof label_data == 'string')
    return label_data;
    
  if(label_data.length == 1) {
    return get_label_field_data(label_data[0], type, name);
  } else {
    var ret = '(';
    
    $.each(label_data, function (i, data) {
      var param = data.param;
      
      if(ret.length > 1) {
        ret += ', ';
      }
      
      if(param) {
        ret += param + ' -> ';
      }
      
      ret += get_label_field_data(data);
    });
  }
  
  return ret + ')';
}

function init_result_list()
{
   show_seqs.grid({
       url: get_app_url() + '/search',
       ajax_method: 'post',
       retrieve: 'get_search',
       total: 'get_search_total',
       params: {
         search: function () { return get_start_search_param(); }
       },
       fieldNames: ['Name'],
       fields: ['name'],
       ordering: {
         name: 'asc',
         update: 'def',
         user_name: 'def'
       },
       links: {
         name: function (row) {
           return get_app_url() + '/sequence/labels/' + row.id;
         },
         user_name: function (row) {
           return get_app_url() + '/profile/view/' + row.update_user_id;
         }
       }
     });
}

$.fn.clickCompoundName = function () {
  return this.each(function () {
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
};

$.fn.clickTermName = function () {
  return this.each(function () {
    node_unselected();
    cant_add_leafs();
    activate_term(this);
  });
};

$(function () {
    
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
    data_date_input = $('#data_date_input');
    date_input = $('#date_input');
    submit_term = $('#submit_term').hide();
    search_human = $('#search_human');
    search_sql = $('#search_sql');
    select_transform = $('#select_transform');
    param_input = $('#param_input');
    data_param = $('#data_param');
    histogram_button = $('#show_histogram_button');
    generate_histogram_type = $('#generate_histogram_type');
    label_result = $('#label_result');
    add_label_button = $('#add_label_button');
    show_result_list = $('#show_result_list');
    show_result_total = $('#show_result_total');
    view_full = $('.view_full', show_result_total);
    
    select_transform.change(update_transform);
    
    date_input.datePickerDate();

    can_add_leafs();

    data_row.focus(operator_was_selected);

    tree_form.submit(function () {
        return true;
    });

    change_tax.click(function () {
        var url = get_app_url() + '/search/tax';
        tb_show('Find taxonomy', url);
        return false;
    });

    change_seq.click(function () {
        var url = get_app_url() + '/search/ref';
        tb_show('Find sequence', url);
        return false;
    });

    $('.term-delete').livequery('click', function () {
        var li_term = $(this).parent().parent();
        var obj = li_term[0].term;
        var parent_li = li_term.parents('li:first');

        li_term.remove();
        
        if(compound_term(obj) && $('.term-name', li_term).size() == 0) {
          // no terms in this subtree
          update_tree();
        } else {
          update_search();
        }

        if(parent_li.size() == 0) {
          we_are_starting = true;
          can_add_leafs();
        } else {
          $('.expand-name:first', parent_li).clickCompoundName();
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

    $('#search_tree .expand-name').livequery('click', function () { $(this).clickCompoundName(); });
    $('#search_tree .term-name').livequery('click', function () { $(this).clickTermName(); });

    label_name.click(enable_label_row);

    position_type_text.click(enable_position_type_select);
    position_type.change(select_position_type);

    operator_text.click(enable_operator_select);

    operator_select.change(operator_was_selected);

    // label autocomplete input
    
    label_row.autocomplete_labels('searchable');
    label_row.autocompleteEmpty(hide_term);
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
    
    // label result
    no_label_result();
    label_result.autocomplete_labels('searchable');
    label_result.autocompleteEmpty(no_label_result);
    label_result.result(function (event, data, formatted) {
      var name = data;
      
      if(!name) {
        no_label_result();
        return;
      }
      
      get_label_by_name(name, function (data) {
        if(data == null)
          no_label_result();
        else
          new_label_result(data);
      });
      
      return false;
    });
    
    add_label_button.click(function () {
      push_new_column_label();
      return false;
    });
    
    // generate label autocomplete input
    var label_hist = $('#generate_label');
    
    no_histogram_label();
    label_hist.autocomplete_labels('searchable');
    label_hist.autocompleteEmpty(no_histogram_label);
    label_hist.result(function (event, data, formatted) {
      var name = data;
      
      if(!name) {
        no_histogram_label();
        return;
      }
      
      get_label_by_name(name, 
        function (data) {
          if(data == null) {
            no_histogram_label();
          } else {
            new_histogram_label(data);
          }
      });
      
      return false;
    });
    
    $('#generate_histogram_form').ajaxForm({
      beforeSubmit: function () {
        if(histogram_label == null)
          return false;
        
        $.blockLoadingUI();
        
        return true;
      },
      success: function (data) {
        
        $.unblockUI();
        
        // HACK to show page data into the thickbox
        tb_show('Histogram', '#TB_inline?inlineId=histogram_data&width=700&height=500');
      },
      target: '#histogram_data'
    });

    // validations
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
    
    $('#reset_form').validate({
      submitHandler: reset_form_submitted
    });
    
  show_seqs.gridEnable();

  restore_old_tree();
  
  // activate view full button
  view_full.click(function () {
     show_result_total.hide().remove();
     show_result_list.show();
     use_result_list = true;
     init_result_list();
     return false;
  })
});
