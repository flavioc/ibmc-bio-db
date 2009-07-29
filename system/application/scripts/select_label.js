$.fn.selectLabel = function (no_select_fun, new_label_fun, type) {
  var selected_label = $('#select_label');
  
  function no_label_present() {
    selected_label.text('(no label selected)');
    no_select_fun();
  }
  
  function changed_label(l) {
    selected_label.hide();
    new_label_fun(l);
  }
  
  no_label_present();
  
  return this.each(function () {
    var $this = $(this);
    
    $this.autocomplete_labels(type);
    $this.autocompleteEmpty(no_label_present);
    
    $this.result(function (event, data, formatted) {
        no_label_present();

        var name = data;

        if(!name) {
          return;
        }

        get_label_by_name(name, 
          function (data) {
            if(data) {
              changed_label(data);
            }
        });

        return false;
    });
  });
};