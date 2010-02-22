
(function ($) {
   
  var MAX_ORDER_ROWS = 2000;

  function show_loading(obj) {
    $('h4', obj).show();
    $('div[@class=pagination]', obj).hide();
    $('img[@class=loader]', obj).show().text('-');
  }

  function hide_loading(obj) {
    $('img[@class=loader]', obj).hide();
  }

  function set_results(obj, total) {
    $('span[@class=total_results]', obj).text(total);
  }

  function get_results_count(obj) {
    return parseInt($('span[@class=total_results]', obj).text());
  }

  function increment_results(obj) {
    var results = get_results_count(obj);

    set_results(obj, results + 1);
  }

  function decrement_results(obj) {
    var results = get_results_count(obj);

    set_results(obj, results - 1);
  }

  function has_delete_row(enableRemove, fields, deleteTag)
  {
    for(var i = 0; i < fields.length; ++i) {
      if(fields[i] == deleteTag) {
        return true;
      }
    }

    return false;
  }
  
  function is_deletable_column(opts, column)
  {
    var ret = false;
    
    $.each(opts.deletableColumns, function (i, field) {
      if(field == column) {
        ret = true;
        return false;
      }
      
      return true;
    });
    
    return ret;
  }

  function get_fields(opts, row)
  {
    var fields = opts.fields;

    if(opts.fieldGenerator) {
      fields = opts.fieldGenerator(row);

      if(opts.enableRemove &&
        !has_delete_row(opts.enableRemove, fields, opts.deleteTag))
      {
        fields.push(opts.deleteTag);
      }
    }

    return fields;
  }

  function create_row_dom(row, opts, obj)
  {
    var fields = get_fields(opts, row);
    var transforms = opts.dataTransform;
    var editables = opts.editables;
    var links = opts.links;
    var types = opts.types;
    var row_tag = {tagName: 'tr'};
    var id = obj[0].id;

    row_tag.id = "row_" + id + "_" +
      (row[opts.idField] == null ? i : row[opts.idField]);

    row_tag.childNodes = new Array(fields.length);

    for(var j = 0; j < fields.length; ++j) {
      var field_name = fields[j];

      if(field_name == opts.deleteTag) {
        if(opts.enableRemove) {
          add_remove_column(obj, opts, row, row_tag, j);
        }
        continue;
      }

      var trans_fun = transforms[field_name];
      var link_fun = links[field_name];
      var field_data = row[field_name];
      var has_link = (link_fun != null);
      var type = types[field_name];

      if(trans_fun != null) {
        field_data = trans_fun(row);
      }

      if(type) {
        if(type == 'boolean') {
          if(field_data == '1' || field_data == 1 || field_data == true) {
            field_data = 'Yes';
          } else if (field_data == '0' || field_data == 0 || field_data == false) {
            field_data = 'No';
          }
        }
      }
      
      if(field_data != null) {
        field_data = field_data.toString();
      }
      
      if(has_link && field_data != null) {
        var href = link_fun(row);

        if(href != null) {
          field_data = '<a href="' + href + '">' + field_data + '</a>';
        }
      }

      if(field_data == null) {
        field_data = '---';
      }

      var class_fun = opts.classFun[field_name];

      if(class_fun) {
        field_data = '<span class="' + class_fun(row) + '">' + field_data + '</span>';
      }

      if(opts.clickFun[field_name]) {
        var className = "field_" + id + '_' + field_name;

        field_data = '<span class="' + className + '">' + field_data + '</span>';
      }

      if(editables[field_name]) {
        field_data = '<span class="' + opts.writeableClass + '"><span class="field_' +
         id + '_' + field_name + '" id="' + field_name + '_' + row[opts.idField] + '" >' + field_data + '</span></span>';
      }

      row_tag.childNodes[j] = {
        tagName: 'td',
        className: ('td_' + id + '_' + opts.originalFields[j]),
        innerHTML: field_data
      };
    }

    return row_tag;
  }

  function add_remove_column(obj, opts, row, row_tag, index)
  {
    var removeFun = opts.enableRemoveFun;
    var id = obj[0].id;
    var more_class = opts.tdClass[opts.deleteTag];
    var del_class = "deletable_column";

    if(more_class) {
      del_class = del_class + " " + more_class;
    }

    if(removeFun == null || removeFun(row)) {
      var delete_id = 'delete_' + id + '_' + row[opts.idField];
      var deleteText = opts.deleteText;
      var trans_del = opts.dataTransform[opts.deleteTag];

      if(trans_del) {
        deleteText = trans_del(row);
      }

      row_tag.childNodes[index] = {
        tagName: 'td',
        className: del_class,
        childNodes: [
          {
            tagName: 'a',
            className: 'deletable',
            href: '#' + delete_id,
            id: delete_id,
            innerHTML: deleteText
          }
        ]
      }
    } else {
      row_tag.childNodes[index] = {
        tagName: 'td',
        className: del_class,
        innerHTML: '---'
      }
    }
  }

  function apply_td_styles(opts, obj) {
    $.each(opts.tdClass, function (field, class_name) {
        var td_class_name = 'td_' + obj[0].id + '_' + field;
        $('td[@class=' + td_class_name + ']', obj).addClass(class_name);
    });
  }

  function apply_th_widths(opts, obj) {
    $.each(opts.width, function (field, width) {
        var th_class_name = 'th_' + obj[0].id + '_' + field;
        $('th[@class=' + th_class_name + ']', obj).css('width', width);
    });
  }

  function activate_ordering(opts, obj) {
    $.each(opts.ordering, function (field, start) {
        var a_class_name = 'ordering_' + obj[0].id + '_' + field;

        $('#' + a_class_name, obj).click(function () {
          var index = null;
          if(opts.method == 'remote') {
            index = 'order_' + field;
          } else {
            index = field;
          }
          
          var current = opts.inner.ordering[index];

          if(current == null) {
            current = start;
          }

          if(current == "desc" || current == 'def') {
            current = "asc";
          } else if(current == "asc") {
            current = "desc";
          }

          var new_ordering = {};
          new_ordering[index] = current;
          opts.inner.ordering = new_ordering;
          
          get_results(obj, opts);
          
          return false;
        });
    });
  }
  
  function activate_column_deletion(opts, obj) {
    $('.delete-column', obj).click(function () {
      var $this = $(this);
      var column = $this.attr('field');
      var hook = opts.deletableColumnsHook[column];
      
      obj.gridRemoveColumn(column);
      
      if(hook)
        hook(column);
        
      obj.gridReload();
    });
  }

  function activate_edition(opts, obj, table) {
    var id = obj[0].id;

    $.each(opts.editables, function (field, how) {
        var className = 'field_' + id + '_' + field;

        $('span[@class='+ className + ']', table)
          .unbind('editable')
          .editable(opts.url + '/edit_' + field, how);
    });

    var confirm_data = {};

    $('a[@class=deletable]', table)
      .unbind('confirm')
      .unbind('click')
      .click(function() {
              var row_id = parse_id($(this).attr('id'));
              var url = opts.url + '/' + opts.remove + '/' + row_id;

              $.post(url, function (data) {
                  var resp = $.evalJSON(data);

                  if(resp) {
                    delete_row(obj, row_id);

                    if(opts.deleteFun) {
                      opts.deleteFun(row_id);
                    }
                  } else {
                    alert('Error deleting item: ' + row_id + ' -> ' + data);
                  }
              });
              
            return false;
      })
      .confirm(confirm_data);
  }

  function delete_row(obj, row_id)
  {
    var id = obj[0].id;
    var tr_id = "row_" + id + "_" + row_id;
    var tr_obj = $('#' + tr_id);

    if(tr_obj.size() == 1 && tr_obj.is(':visible')) {
      tr_obj.fadeOut('slow', function () {
          tr_obj.remove();
      });
      decrement_results(obj);
    }
  }

  function get_table_headers(obj, opts) {
    var names = opts.fieldNames;
    var fields = get_fields(opts, null);
    var ret = new Array(names.length);
    var id = obj[0].id;

    opts.originalFields = fields;

    for(var i = 0; i < names.length; ++i) {
      var name = names[i];

      if(opts.enableRemove && name == opts.deleteTag) {
        name = opts.deleteHeader;
      }

      var inner = name;
      var field_name = fields[i];
      var order = opts.ordering[field_name];

      if(order != null)
        inner = '<a id="ordering_' + id + '_' + field_name + '" href="#">' + inner + '</a>';
      
      if(is_deletable_column(opts, field_name))
        inner += '<span class="delete-column" field="' + field_name + '">x</span>';

      ret[i] = {
        tagName: 'th',
        className: 'th_' + id + '_' + fields[i],
        innerHTML: inner
      };
    }

    return ret;
  }
  
  function get_page_html(page)
  {
    return '<span class="navigate_page"><a href="#">' + page.toString() + '</a></span>';
  }

  function get_data_results(obj, opts, total, start, rows)
  {
    hide_loading(obj);

    var data_place = $('div[@class=data_place]', obj);
    var id = obj[0].id;

    data_place.empty().hide();

    data_place.appendDom([
      {
        tagName: 'table',
        className: 'data',
        childNodes: [
          {
            tagName: 'tbody',
            childNodes: [
              {
                tagName: 'tr',
                childNodes: get_table_headers(obj, opts)
              }
            ]
          }
        ]
      }
    ]);

    data_place.show();

    var table = $('table[@class=data]', data_place);

    if(!opts.paginate) {
      total = rows.length;
      set_results(obj, total);
    }

    table.hide();

    if(rows) {
       for(var i = 0; i < rows.length; ++i) {
          var row = rows[i];
          var row_tag = create_row_dom(row, opts, obj);
          table.appendDom([row_tag]);
       }
    }

    if(opts.paginate) {    
      // enable pagination
      var pagination_div = $('div[@class=pagination]', obj);
      var current_page = Math.round(start / opts.size) + 1;
      var max_pages = Math.ceil(total / opts.size);
      var max_pages_to_show = 10;
      var half_pages_to_show = Math.round(max_pages_to_show / 2);
      var leftmost_page = Math.max(1, current_page - half_pages_to_show);
      var rightmost_page = Math.min(max_pages, current_page + half_pages_to_show);
      
      // try to reuse unused pages from left or right side
      var missing_left = half_pages_to_show - (current_page - leftmost_page);
      if(missing_left > 0) {
        rightmost_page = Math.min(max_pages, current_page + half_pages_to_show + missing_left);
      } else {
        var missing_right = half_pages_to_show - (rightmost_page - current_page);
        
        if(missing_right > 0) {
          leftmost_page = Math.max(1, current_page - half_pages_to_show - missing_right);
        }
      }
      
      // build pagination div
      pagination_div.empty();
      
      if(current_page != 1 && leftmost_page != 1) {
        pagination_div.append(get_page_html(1) + '&nbsp<span class="navigation_markers">&lt;</span>&nbsp');
      }
      
      for(var i = leftmost_page; i <= rightmost_page; ++i) {
        var page_html = null;
        if(i == current_page) {
          page_html = '<span class="current_page">' + current_page.toString() + '</span>';
        } else {
          page_html = get_page_html(i);
        }
        
        if(i != leftmost_page) {
          page_html = '&nbsp;' + page_html;
        }
        
        pagination_div.append(page_html);
      }
      
      if(current_page != max_pages && rightmost_page != max_pages) {
        pagination_div.append('&nbsp<span class="navigation_markers">&gt;</span>&nbsp;' + get_page_html(max_pages));
      }
      
      // put click handlers in place
      $('span.navigate_page', pagination_div).click(function () {
        var page = parseInt($(this).find('a').text());
        
        opts.inner.start = (page - 1) * opts.size;
        get_results(obj, opts);
        return false;
      })
      
      pagination_div.show();
    }

    $.each(opts.inner.shown_columns, function (column, val) {
      if(val) {
        obj.gridShowColumn(column);
      } else {
        obj.gridHideColumn(column);
      }
    });
    
    apply_td_styles(opts, obj);
    apply_th_widths(opts, obj);
    table.fadeIn();
    activate_edition(opts, obj, table);
    activate_ordering(opts, obj);
    activate_column_deletion(opts, obj);

    if(opts.clickFun) {
      $.each(opts.clickFun, function (key, value) {
        var className = 'field_' + id + '_' + key;

        $('span[@class=' + className + ']', obj).each(function (index) {
            var $this = $(this);
            var found = $('a', $this);
            var row = rows[index];

            if(found.length == 1) {
              found.click(function (event) {
                var ret = value(row);
                if(ret) {
                  return true;
                }
                return false;
              });
            } else {
              $this.click(function (event) {
                value(row);
              });
            }
        });
      });
    }

    if(opts.finishedFun) {
      opts.finishedFun(opts, rows);
    }
  }

function get_results(obj, opts) {
  show_loading(obj);
  
  if(opts.inner.total == 0) {
    // show no results
    get_data_results(obj, opts, opts.inner.total, opts.inner.start, {});
    return;
  }

  if(opts.method == 'remote') {
    var data_url = opts.url + '/' + opts.retrieve;
    var params = $.extend({}, opts.inner.params, opts.inner.ordering, opts.params);

    if(opts.paginate) {
      params = $.extend({
         start: opts.inner.start,
         size: Math.min(opts.size, opts.inner.total - opts.inner.start)
      }, params);
    }
     
    $.ajax({
      url: data_url,
      data: params,
      type: opts.ajax_method,
      global: false,
      success: function(data) {
        var rows = $.evalJSON(data);
        
        get_data_results(obj, opts, opts.inner.total, opts.inner.start, rows);
      },
      error: function (request, textstatus, error) {
      }
    });
  } else {
    // local data
    
    // find ordering
    var field_order = null;
    var order_way = null;
    
    $.each(opts.inner.ordering, function (key, value) {
      field_order = key;
      order_way = value;
      return false;
    });
    
    if(field_order) {
      opts.local_data.sort(function (a, b) {
        var a_data = a[field_order];
        var b_data = b[field_order];
        
        var i_a = parseInt(a_data);
        
        if(is_numeric(i_a)) {
          var i_b = parseInt(b_data);
          
          // numbers
          if(is_numeric(i_b)) {
            if(order_way == 'desc')
              return i_b - i_a;
            else
              return i_a - i_b;
          }
        }
        
        // text
        
        if(a_data == b_data)
          return 0;

        if(order_way == 'desc')
          return (b_data < a_data) ? -1 : 1;
        else
          return (a_data < b_data) ? -1 : 1; 
      });
    }
    
    // filter page
    var rows = $.grep(opts.local_data, function (item, index) {
      return index >= opts.inner.start && index < (opts.inner.start + opts.size);
    });
    
    // build html
    get_data_results(obj, opts, opts.inner.total, opts.inner.start, rows);
  }
}

$.fn.gridAdd = function(data) {
  return this.each(function() {
      $this = $(this);
      var opts = this.opts;

      if(opts == null) {
        return;
      }

      var row_tag = create_row_dom(data, opts, $this);
      var data_place = $('div[@class=data_place]', $this);
      var table = $('table[@class=data]', data_place);

      table.appendDom([row_tag]);
      activate_edition(opts, $this, table);
      apply_td_styles(opts, $this);
      increment_results($this);
  });
}

function get_data_column(obj, column)
{
  var td_class = 'td_' + obj.id + '_' + column;
  var th_class = 'th_' + obj.id + '_' + column;
  var th_sel = 'th[@class*=' + th_class + ']';
  var td_sel = 'td[@class*=' + td_class + ']';
  var sel = th_sel + ', ' + td_sel;

  return sel;
}

$.fn.gridColumnFilter = function(column, what) {
  return this.each(function () {
      var $this = $(this);
      var opts = this.opts;

      this.opts.params[column] = what;
  });
};

$.fn.gridFilter = $.fn.gridColumnFilter;

$.fn.gridGetFilter = function (column) {
  var first = this[0];
  var opts = first.opts;
  
  return opts.params[column];
};

$.fn.gridReload = function () {
  return this.each(function () {
      var $this = $(this);
      var opts = this.opts;

      reload_grid(this, $this, opts);
  });
};

$.fn.gridHideColumn = function(column) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    var obj = $(get_data_column(this, column), $this);
    
    opts.inner.shown_columns[column] = false;
    
    obj.hide();
  });
};

$.fn.gridHideDefault = function (type) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;

    $.each(opts.hiddenFields, function (index, field) {
        $this.gridHideColumn(field, type);
    });
  });
};

$.fn.gridShowColumn = function(column) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    var obj = $(get_data_column(this, column), $this);
    var class_name = opts.tdClass[column];
    
    opts.inner.shown_columns[column] = true;
    
    obj.show();
  });
};

$.fn.gridAddColumn = function (field, fieldName, transform) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    
    if($this.gridHasColumn(field))
      return true;
      
    opts.fields.push(field);
    opts.fieldNames.push(fieldName);
    
    if(transform)
      opts.dataTransform[field] = transform;
  });
};

$.fn.gridColumnSetDeletable = function (field, hook) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    
    if(!is_deletable_column(opts, field))
      opts.deletableColumns.push(field);
      
    if(hook)
      opts.deletableColumnsHook[field] = hook;
  })
};

$.fn.gridRemoveColumn = function (field) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    
    if(!$this.gridHasColumn(field))
      return false;
      
    var field_pos = -1;
    
    $.each(opts.fields, function (i, name) {
      if(name == field) {
        field_pos = i;
        return false;
      }
      
      return true;
    });
    
    if(field_pos == -1)
      return false;
      
    opts.fields = $.grep(opts.fields, function (n, i) {
      return i != field_pos;
    });
    
    opts.fieldNames = $.grep(opts.fieldNames, function (n, i) {
      return i != field_pos;
    });
    
    return true;
  });
}

$.fn.gridHasColumn = function (column) {
  var first = this[0];
  var has_field = false;
  
  $.each(first.opts.fields, function (i, field) {
    if(field == column) {
      has_field = true;
      return false;
    }
    
    return true;
  });
  
  return has_field;
};

$.fn.gridShowDefault = function (type) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;

    $.each(opts.hiddenFields, function (index, field) {
        $this.gridShowColumn(field, type);
    });
  });
};

function reload_grid(this_obj, $this, opts)
{
  show_loading($this);
  
  if(opts.method == 'remote') {
    
    $('.reload-button', $this).show();
    
    if(opts.paginate) {
      var url_total = opts.url + '/' + opts.total;
      var params = $.extend({}, opts.inner.params, opts.params);
    
      $.ajax({
        mode: "abort",
        port: "grid" + this_obj.id,
        url: url_total,
        data: params,
        type: opts.ajax_method,
        success: function (data) {
          var total = parseInt(data);
          
          if(total > MAX_ORDER_ROWS)
            opts.inner.params['disable_ordering'] = 't';

          set_results($this, data);
          opts.inner.total = total;
          opts.inner.start = 0;
          get_results($this, opts);
        }
      });
    } else {
      get_results($this, opts);
    }
  } else {
    // local method
    var total = opts.local_data.length;
    
    set_results($this, total);
    opts.inner.total = total;
    opts.inner.start = 0;
    
    // hide reload-button
    $('.reload-button', $this).hide();
    
    get_results($this, opts);
  }
}

$.fn.grid = function(options) {
  return this.each(function() {
    var opts = $.extend(false, this.opts, $.fn.grid.defaults, options, 
      {
        inner: {
          ordering: {},
          total: null,
          start: 0,
          params: {},
          shown_columns: {}
        },
    });
    var $this = $(this);
    var grid = this;
    
    // mark hidden columns
    $.each(opts.hiddenFields, function (i, column) {
      opts.inner.shown_columns[column] = false;
    });

    grid.opts = opts;
    grid.highlight = null;
    $this.show();

    if(opts.enableRemove &&
      !has_delete_row(opts.enableRemove, opts.fields, opts.deleteTag))
    {
      opts.fields.push(opts.deleteTag);
      opts.fieldNames.push(opts.deleteHeader);
    }
    
    // enable remote reload button
    if(opts.method == 'remote') {
      $('.reload-button', $this).click(function () {
        reload_grid(grid, $this, grid.opts);
        
        return false;
      });
    }

    reload_grid(grid, $this, grid.opts);
  });
};

function do_highlight(obj)
{
  $('td', obj).addClass('highlight');
}

function disable_highlight(obj)
{
  $('td', obj).removeClass('highlight');
}

$.fn.gridHighLight = function (row_id) {
  return this.each(function() {
    $this = $(this);
    var old = this.highlight;

    if(old) {
      disable_highlight($(old));
    }

    var row = '#row_' + this.id + '_' + row_id;

    do_highlight($(row));

    this.highlight = row;
  });
};

$.fn.gridDeleteRow = function (row_id) {
  return this.each(function () {
      delete_row($(this), row_id);
  });
};

$.fn.gridEnable = function(options) {
  var paginate = (options ? options.paginate : $.fn.gridEnable.defaults.paginate);
  
  return this.each(function() {
    var $this = $(this);
    
    this.opts = {paginate: paginate, params: {}};

    $this.addClass('grid_box');
    $this.hide();

    var results_tag = '<h4>Results (<span class="total_results">-</span>)<span class="reload-button small-button">reload</span></h4>';
    var img_tag = '<img src="' + get_images_url() + '/loading.gif" class="loader"></img>';
    var data_tag = '<div class="data_place"></div>';

    $this.append(results_tag);
    $this.append(img_tag);
    $this.append(data_tag);
    
    if(paginate) {
      var pagination_tag = '<div class="pagination"></div>';
      
      $this.append(pagination_tag);
      
      $('div[@class=pagination]', $this).hide();
    }

    $('div[@class=data_place]', $this).hide();
    $('img[@class=loader]', $this).hide();
    $('h4', $this).hide();
  });
}

$.fn.grid.defaults = {
  method: 'remote', // 'remote' or 'local'
  local_data: null,
  
  size: get_paging_size(),
  start: 0,
  
  // remote data
  url: '',
  total: 'total',
  retrieve: 'get',
  
  fields: [],
  fieldNames: [],
  fieldGenerator: null,
  dataTransform: {},
  links: {},
  editables: {},
  remove: 'delete',
  removeMessage: null,
  what: null,
  enableRemove: false,
  types: {},
  enableRemoveFun: null,
  finishedFun: null,
  deleteTag: '$delete',
  deleteText: 'Delete',
  deleteHeader: 'Delete',
  idField: 'id',
  clickFun: {},
  hiddenFields: [],
  writeableClass: 'writeable',
  classFun: {},
  deleteFun: null,
  tdClass: {},
  width: {},
  ordering: {},
  params: {},
  deletableColumns: [],
  deletableColumnsHook: {},
  ajax_method: 'get'
}

$.fn.gridEnable.defaults = {
  paginate: true
};

})(jQuery);
