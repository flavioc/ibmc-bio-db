/*
@author: Remy Sharp / http://remysharp.com
@date: 2007-04-21
@usage:
$('img').zoom('200%', '200%');
$('img').zoom('300px', '200px');
$('img').zoom(300);
*/
jQuery.fn.zoom = function(height, width) {
  if (!width && height) {
    width = height;
  } else if (!width && !height) {
    width = height = '100%';
  }
  
  function toem(px) {
    px = parseFloat(px);
    return (px * 0.0626).toString() + 'em';
  }
  
  function getLen(elm, side, target) {
    var l = 1;
    if (target.indexOf('%') === -1) {
      l = toem(target);
    } else {
      l = toem(elm[side] * (parseFloat(target)/100));
    }
    
    return l;
  }
  
  return this.each(function() {
    var hem = getLen(this, 'height', height);
    var wem = getLen(this, 'width', width);
    
    jQuery(this).css({ height: hem, width: wem });
  });
};