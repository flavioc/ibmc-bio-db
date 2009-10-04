<h2>Tree list</h2>

{literal}
<script>
$(function () {
  var base_site = get_app_url() + '/tree';
  var show_trees = $('#show_trees');
  var name_field = $('#name');
  var user_field = $('#user');
  var fieldNames = ['Name', 'Export', 'Last update', 'User'];
  var fields = ['name', 'export', 'update', 'user_name'];
  
  if(get_logged_in()) {
    fieldNames.push('Root');
    fields.push('add');
  }

  show_trees
  .gridEnable({paginate: false})
  .grid({
    url: base_site,
    retrieve: 'get_all',
    fieldNames:  fieldNames,
    fields: fields,
    dataTransform: {
      add: function (row) {
        return img_add;
      },
      'export': function (row) {
        return img_export;
      }
    },
    ordering: {
      name: 'asc',
      update: 'def',
      user_name: 'def'
    },
    links: {
      add: function (row) {
        return get_app_url() + '/taxonomy/add?tree=' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      },
      name: function (row) {
        return base_site + '/view/' + row.id;
      },
      'export': function (row) {
        return base_site + '/export/' + row.id;
      }
    },
    tdClass: {
      update: 'centered',
      add: 'centered',
      'export': 'centered'
    },
    width: {
      add: w_add,
      user_name: w_user,
      update: w_update,
      'export': w_export
    },
    params: {
      name: function () { return name_field.val(); },
      user: function () { return user_field.val(); }
    }
  });

  function when_submit()
  {
    show_trees.gridReload();
  }
  
  $("#form_search").validate({
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });
});
</script>
{/literal}

{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row type=select data=$users name=user msg='User:' key=id blank=yes}
{form_submit name=submit_search msg=Filter}
{form_end}

<div id="show_trees"></div>

{if $logged_in}
  {button name="add_tree" to="tree/add" msg="Add tree"}
{/if}