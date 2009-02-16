<h2>Tree list</h2>

{literal}
<script>
  $(document).ready(function () {

  var base_site = get_app_url() + '/tree';

  function when_submit(form) {
    var new_name = $('#new_name').val();

    $.post(base_site + '/add/' + new_name, function(data) {
      var obj = $.evalJSON(data);

      if(obj) {
        $('#show_trees').gridAdd(obj);
      }
    });
  }

  $("#form_add").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        }
    },
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  $('#show_trees')
  .gridEnable({paginate: false})
  .grid({
    url: base_site,
    retrieve: 'get_all',
    fieldNames: ['Name'],
    fields: ['name'],
    editables: {name: {
                        select : true,
                        submit : 'OK',
                        cancel : 'cancel',
                        cssclass : "editable",
                        width: "200px"
                        }
               },
    countRemove: 'total_taxonomies',
    what: 'tree',
    removeAssociated: 'taxonomies',
    enableRemove: true
  });
});
</script>
{/literal}

<div id="show_trees"></div>

<p>
{form_open name=form_add}
{form_label for=name msg='New tree: '}
{form_input name=name id="new_name"}
{form_label_error for=name id=name_error}
<br />
{form_submit name=submit_add msg=Add}
{form_end}
</p>
