(function ($) {

$.fn.plot = function (data, options) {
  return this.each(function () {
    var $this = $(this);
    var graph_width = options['width'];
    var MAX_LABEL = 40;
    
    // find maximum value and maximum length what string
    var maximum = 0;
    var maximum_length = 0;
    
    $.each(data, function(x, y) {
      if(y > maximum)
        maximum = y;
      
      var length = Math.min(MAX_LABEL, x.toString().length);
      if(length > maximum_length)
        maximum_length = length;
    });
    
    var html = '<div class="graph" style="width:' + graph_width + 'px;">';
    var width_what = maximum_length * 10;
    var margin_bar = width_what + 10;
    var bar_width = graph_width - margin_bar - 4 ;
    
    $.each(data, function (what, total) {
      var dec = Math.max(0.05, total / maximum);
      var width = parseInt(dec * bar_width);
      
      what = what.toString().substr(0, MAX_LABEL);
      
      html = html + '<div class="graph-line"><div class="graph-label">' + what + '</div>';
      html = html + '<div class="graph-bar" style="width: ' + width + 'px;">' + total + '</div>';
      html = html + '</div>';
    });
    
    html = html + '</div>';
    
    var $html = $(html);
    
    $('div.graph-label', $html).css('width', width_what.toString() + 'px');
    $('div.graph-bar', $html).css('margin-left', margin_bar.toString() + 'px');
    $('div.graph-label', $html).hover(function () {
      $(this).css('background-color', 'yellow');
      $(this).next().css('background-color', 'yellow').css('color', 'black');
    },
    function () {
      $(this).css('background-color', 'white');
      $(this).next().css('background-color', '#c00').css('color', 'white');
    });
    
    $('div.graph-bar', $html).hover(function () {
      $(this).css('background-color', 'yellow').css('color', 'black');
      $(this).prev().css('background-color', 'yellow');
    },
    function () {
      $(this).css('background-color', '#c00').css('color', 'white');
      $(this).prev().css('background-color', 'white');
    });
    
    $this.empty();
    $html.appendTo($this);
    
    return true;
  });
};
  
})(jQuery);