
(function ($) {

  function show_loading(obj) {
    $('h3', obj).show();
    $('div[@class=navigation]', obj).hide();
    $('img[@class=loader]', obj).show().text('-');
  }

  function hide_loading(obj) {
    $('img[@class=loader]', obj).hide();
  }

  function get_table_headers(opts) {
    var names = opts.fieldNames;
    var ret = new Array(names.length);

    for(var i = 0; i < names.length; ++i) {
      ret[i] = {tagName: 'th', innerHTML: names[i]};
    }

    return ret;
  }

  function get_results(obj, opts, total, start) {
    var data_url = opts.url + '/' + opts.retrieve;

    show_loading(obj);

    $.ajax({
      mode: "abort",
      port: "grid" + this.id,
      url: data_url,
      data: $.extend({
        start: start,
        size: opts.size
      }, opts.params),
      success: function(data) {
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

        var rows = $.evalJSON(data);
        var fields = opts.fields;
        var transforms = opts.dataTransform;
        var links = opts.links;
        var rowId = opts.rowId;
        var table = $('table[@class=data]', data_place);

        table.hide();

        for(var i = 0; i < rows.length; ++i) {
          var row = rows[i];
          var row_tag = {tagName: 'tr'};

          if(rowId != null) {
            var tr_id = rowId(row);

            row_tag.id = tr_id;
          }

          row_tag.childNodes = new Array(fields.length);

          for(var j = 0; j < fields.length; ++j) {
            var field_name = fields[j];
            var trans_fun = transforms[field_name];
            var link_fun = links[field_name];
            var field_data = row[field_name];
            var has_link = (link_fun != null);

            if(trans_fun != null) {
              field_data = trans_fun(row);
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
          }

          table.appendDom([row_tag]);
        }

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

        table.fadeIn();
      }
    });
  }

  $.fn.grid = function(options) {
    var opts = $.extend({}, $.fn.grid.defaults, options);

    return this.each(function() {
      $this = $(this);
      var url_total = opts.url + '/' + opts.total;

      show_loading($this);

      $.ajax({
        mode: "abort",
        port: "grid" + this.id,
        url: url_total,
        data: opts.params,
        success: function (data) {
          var total = parseInt(data);

          $('span[@class=total_results]', $this).text(data);
          get_results($this, opts, total, 0);
        }
      });
    });
  }

  $.fn.gridEnable = function(options) {
    return this.each(function() {
      $this = $(this);
      var results_tag = '<h3>Results (<span class="total_results">-</span>)</h3>';
      var img_tag = '<img src="' + get_images_url() + '/loading.gif" class="loader"></img>';
      var previous_tag = '<a href="#" class="nav_previous">&lt;&lt; Previous</a>';
      var next_tag = '<a href="#" class="nav_next">Next &gt;&gt;</a>';
      var data_tag = '<div class="data_place"></div>';
      var navigation_tag = '<div class="navigation">' +
          previous_tag + next_tag + '</div>';

      $this.append(results_tag);
      $this.append(img_tag);
      $this.append(data_tag);
      $this.append(navigation_tag);

      $('div[@class=data_place]', $this).hide();
      $('div[@class=navigation]', $this).hide();
      $('img[@class=loader]', $this).hide();
      $('h3', $this).hide();
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
    rowId: null
  }

 })(jQuery);
