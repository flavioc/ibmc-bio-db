var current_label = null;

$(function () {
  var label_input = $('#label');
  var submit = $('#submit_delete_label');
  
  label_input.selectLabel(function () {},
    function (label) {
      current_label = label;
    },
    'deletable');
  
  submit.click(function () {
    if(current_label == null) {
      return false;
    }
    
    $.ajaxprompt(get_app_url() + '/delete_labels/delete_dialog/' + current_label.id,
      {
        buttons: {Yes: true, No: false},
        submit: function (v) {
          if(v) {
            $.blockLoadingUI();
            $.post(get_app_url() + '/delete_labels/delete',
              {
                search: $('input[name=search]').val(),
                transform: $('input[name=transform]').val(),
                label_id: current_label.id
              },
              function () {
                $.unblockUI();
              },
              'script'
            );
          }

          return true;
        }
      });
    
    return false;
  })
});