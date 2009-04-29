<h2>Label list</h2>

{literal}
<script>
$(document).ready(function () {
  var base_site = get_app_url() + "/label";
  var changed = true;

  $('#label_show').gridEnable();

  function when_submit()
  {
    if(changed) {
      $('#label_show').grid({
        url: base_site,
        retrieve: 'get_all',
        total: 'count_total',
        params: {
          name: function () { return $('#name').val(); }
        },
        fieldNames: ['Name', 'Type', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
        fields: ['name', 'type', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
        links: {
          name: function (row) {
            return base_site + '/view/' + row.id;
          }
        },
        tdClass: {
          multiple: 'centered',
          editable: 'centered',
          deletable: 'centered',
          auto_on_modification: 'centered',
          auto_on_creation: 'centered',
          must_exist: 'centered'
        },
        width: {
          multiple: w_boolean,
          editable: w_boolean,
          deletable: w_boolean,
          auto_on_creation: w_boolean,
          auto_on_modification: w_boolean,
          must_exist: w_boolean,
          type: w_type
        },
        types: {
          must_exist: 'boolean',
          auto_on_creation: 'boolean',
          auto_on_modification: 'boolean',
          deletable: 'boolean',
          editable: 'boolean',
          multiple: 'boolean'
        },
        ordering: {
          name: 'asc',
          type: 'def'
        }
      });
    }

    changed = false;
  }

  $('#name').change(function () { changed = true; });

  $("#form_search").validate({
    rules: {
      name: {
        minlength: 0,
        maxlength: 255
      }
    },
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  when_submit();

});
</script>
{/literal}

<p>
{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_submit name=submit_search msg=Search}
{form_end}
</p>

<p>
<div id="label_show">
</div>
</p>

{button name="add_label" msg="Add new" to="label/add"}
