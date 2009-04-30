<h2>Label list</h2>

{literal}
<script>
$(document).ready(function () {
  var base_site = get_app_url() + "/label";
  var changed = false;
  var name_field = $('#name');
  var type_field = $('#type');
  var user_field = $('#user');
  var grid = $('#label_show');
  
  function changed_function ()
  {
    changed = true;
  }

  function when_submit()
  {
    if(changed) {
      var name_val = name_field.val();
      var type_val = type_field.val();
      var user_val = user_field.val();

      grid.gridColumnFilter('name', name_val);
      grid.gridColumnFilter('type', type_val);
      grid.gridColumnFilter('user', user_val);
      grid.gridReload();
    }

    changed = false;
  }

  // watch changes
  name_field.change(changed_function);
  type_field.change(changed_function);
  user_field.change(changed_function);

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

  grid.gridEnable();
  grid.grid({
    url: base_site,
    retrieve: 'get_all',
    total: 'count_total',
    fieldNames: ['Name', 'Type', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple', 'User'],
    fields: ['name', 'type', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'user_name'],
    links: {
      name: function (row) {
        return base_site + '/view/' + row.id;
      },
      user_name: function(row) {
        return build_user_url(row.update_user_id);
      }
    },
    tdClass: {
      multiple: 'centered',
      editable: 'centered',
      deletable: 'centered',
      auto_on_modification: 'centered',
      auto_on_creation: 'centered',
      must_exist: 'centered',
      user_name: 'centered',
    },
    width: {
      multiple: w_boolean,
      editable: w_boolean,
      deletable: w_boolean,
      auto_on_creation: w_boolean,
      auto_on_modification: w_boolean,
      must_exist: w_boolean,
      type: w_type,
      user_name: w_user
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
});
</script>
{/literal}

<p>
{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row type=select data=$types name=type msg='Type:' key=name blank=yes}
{form_row type=select data=$users name=user msg='User:' key=id blank=yes}
{form_submit name=submit_search msg=Filter}
{form_end}
</p>

<p>
<div id="label_show">
</div>
</p>

{button name="add_label" msg="Add new" to="label/add"}
