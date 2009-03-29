
(function ($) {

 $.ajaxprompt = function(url, url_params_, options_) {
  var options = options_;
  var url_params = url_params_;

  if(options_ == null) {
    url_params = {};
    options = url_params_;
  }

  $.get(url, url_params, function (data) {
    $.prompt(data, options);
  });
 };

})(jQuery);
