
(function ($) {

  function show_loading(obj) {
    $('h4', obj).show();
    $('div[@class=navigation]', obj).hide();
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
          if(field_data == '1') {
            field_data = 'Yes';
          } else if (field_data == '0') {
            field_data = 'No';
          }
        }
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
        class: 'td_' + id + '_' + opts.originalFields[j],
        innerHTML: field_data
      };
    }

    return row_tag;
  }

  function add_remove_column(obj, opts, row, row_tag, index)
  {
    var removeFun = opts.enableRemoveFun;
    var id = obj[0].id;

    if(removeFun == null || removeFun(row)) {
      var delete_id = 'delete_' + id + '_' + row[opts.idField];

      row_tag.childNodes[index] = {
        tagName: 'td',
        class: 'deletable_column',
        childNodes: [
          {
            tagName: 'a',
            class: 'deletable',
            href: '#' + delete_id,
            id: delete_id,
            innerHTML: opts.deleteText
          }
        ]
      }
    } else {
      row_tag.childNodes[index] = {
        tagName: 'td',
        class: 'deletable_column',
        innerHTML: '---'
      }
    }
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

    if(opts.removeMessage != null) {
      confirm_data.msg = function (target, msg) {
        var id = parseInt(parse_id(target.id));

        opts.removeMessage(id, msg);
      }
    } else if(opts.countRemove != null) {
      confirm_data.msg = function (target, msg) {
        var id = parseInt(parse_id(target.id));

        $.get(opts.url + '/' + opts.countRemove + '/' + id,
            {},
            function (data) {
              msg.text('This ' + opts.what + ' is associated with ' + data + ' ' + opts.removeAssociated + '. Delete it?');
        });
      }
    }

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
        name = opts.deleteText;
      }

      ret[i] = {
        tagName: 'th',
        class: 'th_' + id + '_' + fields[i],
        innerHTML: name
      };
    }

    return ret;
  }

  function get_data_results(obj, opts, total, start, rows) {
    hide_loading(obj);

    var data_place = $('div[@class=data_place]', obj);
    var id = obj[0].id;

    data_place.empty().hide();

    data_place.appendDom([
      {
        tagName: 'table',
        class: 'data',
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

    for(var i = 0; i < rows.length; ++i) {
      var row = rows[i];
      var row_tag = create_row_dom(row, opts, obj);
      table.appendDom([row_tag]);
    }

    if(opts.paginate) {
      var next = $('a[@class=nav_next]', obj);
      var next_start = start + opts.size;
      var previous = $('a[@class=nav_previous]', obj);
      var previous_start = start - opts.size;
      var has_next = (next_start < total);
      var has_previous = (previous_start >= 0);

      next.unbind();
      previous.unbind();

      if(has_next) {
        if(next.is(':hidden')) {
          next.fadeIn();
        }

        next.click(function () {
            get_results(obj, opts, total, next_start);
        });
      } else {
        if(!next.is(':hidden')) {
          next.fadeOut();
        }
      }

      if(has_previous) {
        if(previous.is(':hidden')) {
          previous.fadeIn();
        }

        previous.click(function() {
            get_results(obj, opts, total, previous_start);
        });
      } else {
        if(!previous.is(':hidden')) {
          previous.fadeOut();
        }
      }

      var navigation = $('div[@class=navigation]', obj);
      var has_nav = (has_next || has_previous);

      if(has_nav) {
        if(navigation.is(':hidden')) {
          navigation.slideDown('slow');
        }
      } else {
        if(!navigation.is(':hidden')) {
          navigation.slideUp('slow');
        }
      }
    }

    obj.gridHideDefault();
    table.fadeIn();
    activate_edition(opts, obj, table);

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

function get_results(obj, opts, total, start) {
  var data_url = opts.url + '/' + opts.retrieve;

  show_loading(obj);

  var params = opts.params;

  if(opts.paginate) {
    params = $.extend({
      start: start,
      size: Math.min(opts.size, total - start)
    }, params);
  }

  $.ajax({
    url: data_url,
    data: params,
    type: 'get',
    global: false,
    success: function(data) {
      var rows = $.evalJSON(data);

      get_data_results(obj, opts, total, start, rows);
    },
    error: function (request, textstatus, error) {
      alert("ERROR!");
      alert(textstatus);
    }
  });
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
      increment_results($this);
  });
}

function get_data_column (obj, column)
{
  var td_class = 'td_' + obj.id + '_' + column;
  var th_class = 'th_' + obj.id + '_' + column;
  var th_sel = 'th[@class=' + th_class + ']';
  var td_sel = 'td[@class=' + td_class + ']';
  var sel = th_sel + ', ' + td_sel;

  return sel;
}

$.fn.gridHideColumn = function(column, type) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    var obj = $(get_data_column(this, column), $this);
    
    if(type) {
      obj.fadeOut(type);
    } else {
      obj.hide();
    }
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

$.fn.gridShowColumn = function(column, type) {
  return this.each(function () {
    var $this = $(this);
    var opts = this.opts;
    var obj = $(get_data_column(this, column), $this);
    
    if(type) {
      obj.fadeIn(type);
    } else {
      obj.show();
    }
  });
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

$.fn.grid = function(options) {
  var opts = $.extend({}, $.fn.grid.defaults, options);

  return this.each(function() {
    $this = $(this);

    this.opts = opts;
    this.highlight = null;
    $this.show();

    var url_total = opts.url + '/' + opts.total;

    if(this.paginate != null) {
      opts.paginate = this.paginate;
    }

    if(opts.enableRemove &&
      !has_delete_row(opts.enableRemove, opts.fields, opts.deleteTag))
    {
      opts.fields.push(opts.deleteTag);
      opts.fieldNames.push(opts.deleteText);
    }

    show_loading($this);

    if(opts.paginate) {
      $.ajax({
        mode: "abort",
        port: "grid" + this.id,
        url: url_total,
        data: opts.params,
        success: function (data) {
          var total = parseInt(data);

          set_results($this, data);
          get_results($this, opts, total, 0);
        }
      });
    } else {
      get_results($this, opts, null, 0);
    }
  });
}

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
  var opts = $.extend({}, $.fn.gridEnable.defaults, options);

  return this.each(function() {
    $this = $(this);

    $this.addClass('grid_box');
    $this.hide();

    var results_tag = '<h4>Results (<span class="total_results">-</span>)</h4>';
    var img_tag = '<img src="' + get_images_url() + '/loading.gif" class="loader"></img>';
    var data_tag = '<div class="data_place"></div>';

    $this.append(results_tag);
    $this.append(img_tag);
    $this.append(data_tag);

    if(opts.paginate) {
      var previous_tag = '<a href="#" class="nav_previous">&lt;&lt; Previous</a>';
      var next_tag = '<a href="#" class="nav_next">Next &gt;&gt;</a>';
      var navigation_tag = '<div class="navigation">' +
          previous_tag + next_tag + '</div>';

      $this.append(navigation_tag);

      $('div[@class=navigation]', $this).hide();
    }

    $('div[@class=data_place]', $this).hide();
    $('img[@class=loader]', $this).hide();
    $('h4', $this).hide();

    this.paginate = opts.paginate;
  });
}

$.fn.grid.defaults = {
  total: 'total',
  size: get_paging_size(),
  start: 0,
  retrieve: 'get',
  fields: [],
  fieldNames: [],
  fieldGenerator: null,
  url: '',
  params: {},
  dataTransform: {},
  links: {},
  editables: {},
  remove: 'delete',
  removeMessage: null,
  countRemove: null,
  what: null,
  removeAssociated: null,
  enableRemove: false,
  types: {},
  enableRemoveFun: null,
  finishedFun: null,
  deleteTag: '$delete',
  deleteText: 'Delete',
  idField: 'id',
  clickFun: {},
  hiddenFields: [],
  writeableClass: 'writeable',
  classFun: {},
  deleteFun: null
}

$.fn.gridEnable.defaults = {
  paginate: true
};

})(jQuery);
