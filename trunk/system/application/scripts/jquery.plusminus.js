
(function ($) {

var a_plus = 'a[@class=plus_click]';
var a_minus = 'a[@class=minus_click]';
var img_plus = 'img[@class=plus_image]';
var img_minus = 'img[@class=minus_image]';

var plus = a_plus + ', ' + img_plus;
var minus = a_minus + ', ' + img_minus;
var img_all = img_plus + ', ' + img_minus;

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

    var plus_tag = '<a class="plus_click" href="#"><img class="plus_image" src="' + get_images_url() + '/plus.png" />';

    if(opts.enableText) {
      plus_tag += ' ' + opts.plusText;
    }

    plus_tag += '</a>';

    var minus_tag = '<a class="minus_click" href="#"><img class="minus_image" src="' + get_images_url() + '/minus.png" />';

    if(opts.enableText) {
      minus_tag += ' ' + opts.minusText;
    }

    minus_tag += '</a>';

    $this.append(plus_tag + minus_tag);
    
    var zoom = opts.zoom + '%';

    $(img_all, $this).zoom(zoom, zoom);

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
  enabled: false
};

})(jQuery);

