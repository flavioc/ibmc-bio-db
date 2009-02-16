
(function ($) {

  function show_loading(obj) {
    $('h3', obj).show();
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

  function create_row_dom(row, opts, obj) {
    var fields = opts.fields;
    var transforms = opts.dataTransform;
    var editables = opts.editables;
    var links = opts.links;
    var types = opts.types;
    var row_tag = {tagName: 'tr'};

    row_tag.id = "row_" + obj[0].id + "_" +
      (row.id == null ? i : row.id);

    var fields_length = fields.length;

    if(opts.enableRemove) {
      fields_length++;
    }

    row_tag.childNodes = new Array(fields_length);

    for(var j = 0; j < fields.length; ++j) {
      var field_name = fields[j];

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

      if(has_link) {
        var href = link_fun(row);

        if(href != null) {
          field_data = '<a href="' + href + '">' + field_data + '</a>';
        }
      }

      row_tag.childNodes[j] = {
        tagName: 'td',
        innerHTML: field_data
      };

      if(editables[field_name]) {
        row_tag.childNodes[j].class = 'editable_' + field_name;
        row_tag.childNodes[j].id = 'field_' + row_tag.id;
      }
    }

    if(opts.enableRemove) {
      var removeFun = opts.enableRemoveFun;

      if(removeFun == null || removeFun(row)) {
        var delete_id = 'delete_' + obj[0].id + '_' + row.id;

        row_tag.childNodes[fields_length-1] = {
            tagName: 'td',
            class: 'deletable_column',
            childNodes: [
              {
                tagName: 'a',
                class: 'deletable',
                href: '#' + delete_id,
                id: delete_id,
                innerHTML: 'Delete'
               }
             ]
           }
      } else {
        row_tag.childNodes[fields_length-1] = {
          tagName: 'td',
          class: 'deletable_column',
          innerHTML: '---'
        }
      }
    }

    return row_tag;
  }

  function activate_edition(opts, obj, table) {
    $.each(opts.editables, function (field, how) {
        $('td[@class=editable_' + field + ']', table)
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
              var id = parse_id($(this).attr('id'));
              var url = opts.url + '/' + opts.remove + '/' + id;

              $.post(url, function (data) {
                  if(is_ok(data)) {
                    var tr_id = "row_" + obj[0].id + "_" + id;

                    $('#' + tr_id).fadeOut('slow');
                    decrement_results(obj);
                  } else {
                    alert('Error deleting item: ' + id);
                  }
              });
      })
      .confirm(confirm_data);
  }

  function get_table_headers(opts) {
    var names = opts.fieldNames;
    var length = names.length + (opts.enableRemove ? 1 : 0);
    var ret = new Array(length);

    for(var i = 0; i < names.length; ++i) {
      ret[i] = {tagName: 'th', innerHTML: names[i]};
    }

    if(opts.enableRemove) {
      ret[names.length] = {tagName: 'th', innerHTML: 'Delete'};
    }

    return ret;
  }

  function get_data_results(obj, opts, total, start, rows) {
    hide_loading(obj);

    var data_place = $('div[@class=data_place]', obj);

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
                childNodes: get_table_headers(opts)
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

    table.fadeIn();
    activate_edition(opts, obj, table);
  }

function get_results(obj, opts, total, start) {
  var data_url = opts.url + '/' + opts.retrieve;

  show_loading(obj);

  var params = opts.params;

  if(opts.paginate) {
    params = $.extend({
      start: start,
      size: opts.size
    }, params);
  }

  $.ajax({
    mode: "abort",
    port: "grid" + this.id,
    url: data_url,
    data: params,
    success: function(data) {
      var rows = $.evalJSON(data);

      get_data_results(obj, opts, total, start, rows);
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
      table.appendDom([{tagName: 'tr'}]);
      activate_edition(opts, $this, table);
      increment_results($this);
  });
}

$.fn.grid = function(options) {
  var opts = $.extend({}, $.fn.grid.defaults, options);

  return this.each(function() {
    $this = $(this);

    this.opts = opts;

    var url_total = opts.url + '/' + opts.total;

    if(this.paginate != null) {
      opts.paginate = this.paginate;
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

$.fn.gridEnable = function(options) {
  var opts = $.extend({}, $.fn.gridEnable.defaults, options);

  return this.each(function() {
    $this = $(this);
    var results_tag = '<h3>Results (<span class="total_results">-</span>)</h3>';
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
    $('h3', $this).hide();

    this.paginate = opts.paginate;
  });
}

$.fn.grid.defaults = {
  total: 'total',
  size: 20,
  start: 0,
  retrieve: 'get',
  fields: [],
  fieldNames: [],
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
  enableRemoveFun: null
}

$.fn.gridEnable.defaults = {
  paginate: true
};

})(jQuery);
