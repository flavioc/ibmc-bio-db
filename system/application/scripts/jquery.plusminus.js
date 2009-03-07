
(function ($) {

var a_plus = 'a[@class=plus_click]';
var a_minus = 'a[@class=minus_click]';
var img_plus = 'img[@class=plus_image]';
var img_minus = 'img[@class=minus_image]';
var span_plus = 'span[@class=plus_text]';
var span_minus = 'span[@class=minus_text]';

var plus = a_plus + ', ' + img_plus;
var minus = a_minus + ', ' + img_minus;
var img_all = img_plus + ', ' + img_minus;
var span_all = span_plus + ', ' + span_minus;

var plus_image = get_images_url() + '/plus.png';
var minus_image = get_images_url() + '/minus.png';

function preload_image(href)
{
  image_obj = new Image();
  image_obj.src = href;
}

// preload images
preload_image(plus_image);
preload_image(minus_image);

function show_plus(obj)
{
  $(plus, obj).show();
}

function hide_plus(obj)
{
  $(plus, obj).hide();
}

function show_minus(obj)
{
  $(minus, obj).show();
}

function hide_minus(obj)
{
  $(minus, obj).hide();
}

$.fn.minusPlus = function(options) {
  var opts = $.extend({}, $.fn.minusPlus.defaults, options);

  return this.each(function () {
    var $this = $(this);

    $this.addClass('minus_plus_box');
    $this.hide();

    var plus_tag = '<a class="plus_click" href="#">';

    if(opts.enableImage) {
      plus_tag += '<img class="plus_image" src="' + plus_image + '" />';
    }

    if(opts.enableText) {
      plus_tag += ' <span class="plus_text">' + opts.plusText + '</span>';
    }

    plus_tag += '</a>';

    var minus_tag = '<a class="minus_click" href="#">';

    if(opts.enableImage) {
      minus_tag += '<img class="minus_image" src="' + minus_image + '" />';
    }

    if(opts.enableText) {
      minus_tag += ' <span class="minus_text">' + opts.minusText + '</span>';
    }

    minus_tag += '</a>';

    var all_tags = plus_tag + minus_tag;

    $this.append(all_tags);
    
    var zoom = opts.zoom + '%';

    if(opts.enableImage) {
      $(img_all, $this).zoom(zoom, zoom);
    }
    if(opts.enableText) {
      $(span_all, $this).css('font-size', zoom);
    }

    if(opts.enabled) {
      hide_plus($this);
      opts.plusEnabled();
    } else {
      hide_minus($this);
    }

    $(a_plus, $this).click(function (event) {
        hide_plus($this);
        show_minus($this);
        opts.plusEnabled();

        return false;
    });

    $(a_minus, $this).click(function (event) {
        hide_minus($this);
        show_plus($this);
        opts.minusEnabled();

        return false;
    });

    $this.show();
  });
};

$.fn.minusPlus.defaults = {
  plusEnabled: function () { return null; },
  minusEnabled: function () { return null; },
  plusText: 'Show',
  minusText: 'Hide',
  enableText: true,
  zoom: 100,
  enabled: false,
  enableImage: true
};

})(jQuery);

