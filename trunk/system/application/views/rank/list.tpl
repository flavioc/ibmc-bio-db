<h2>Rank list</h2>

{literal}
<script>
$(document).ready(function () {
  var base_site = get_app_url() + '/rank/';
  var show_ranks = $('#show_ranks');

  show_ranks
  .gridEnable()
  .grid({
    url: get_app_url() + '/rank',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Parent', 'Last update', 'User', 'Taxonomy', 'Child'],
    fields: ['rank_name', 'rank_parent_name', 'update', 'user', 'add', 'add_child'],
    tdClass: {
      update: 'centered',
      add: 'centered',
      add_child: 'centered',
      user: 'centered'
    },
    width: {
      add: w_add,
      add_child: w_add,
      last_user: w_user,
      update: w_update,
      rank_name: '26%'
    },
    dataTransform: {
      add: function (row) {
        return img_add;
      },
      add_child: function (row) {
        return img_add;
      },
      rank_parent_name: function (row) {
        if (row.rank_parent_name == null) {
          return null;
        } else {
          return row.rank_parent_name;
        }
      },
      user: function (row) {
        if(row.update_user_id == null) {
          return null;
        } else {
          return row.user_name;
        }
      }
    },
    links: {
      rank_name: function (row) {
        return get_app_url() + '/rank/view/' + row.rank_id;
      },
      add: function (row) {
        return get_app_url() + '/taxonomy/add?rank=' + row.rank_id;
      },
      add_child: function (row) {
        return get_app_url() + '/rank/add?parent_id=' + row.rank_id;
      },
      user: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    ordering: {
      rank_name: 'asc',
      rank_parent_name: 'def',
      update: 'def',
      user: 'def'
    },
    total: 'get_total',
    idField: 'rank_id'
  });

  var changed = false;
  var name_field = $('#name');
  var parent_field = $('#parent_name');
  var user_field = $('#user');

  function changed_function ()
  {
    changed = true;
  }

  function when_submit()
  {
    if(changed) {
      var name_val = name_field.val();
      var parent_val = parent_field.val();
      var user_val = user_field.val();

      show_ranks.gridColumnFilter('name', name_val);
      show_ranks.gridColumnFilter('parent_name', parent_val);
      show_ranks.gridColumnFilter('user', user_val);
      show_ranks.gridReload();
    }

    changed = false;
  }

  $("#form_search").validate({
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  name_field.change(changed_function);
  parent_field.change(changed_function);
  user_field.change(changed_function);
  
});
</script>
{/literal}

{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row name=parent_name msg='Parent:'}
{form_row type=select data=$users name=user msg='User:' key=id blank=yes}
{form_submit name=submit_search msg=Filter}
{form_end}

<div id="show_ranks"></div>

{form_open name=form_export to="rank/export"}
{form_hidden name=export_name}
{form_hidden name=export_parent}
{form_hidden name=export_user}
{form_submit name="submit_export" msg="Export all"}
{form_end}

{button name="add_rank" to="rank/add" msg="Add rank"}

{literal}<style>
#form_export, #form_add_rank {
  display: inline;
}
</style>
<script>
$(function () {
  $('#submit_export').click(function () {
    $('input[name=export_name]').val($('#name').val());
    $('input[name=export_parent]').val($('#parent_name').val());
    $('input[name=export_user]').val($('#user').val());
    return true;
  });
});
</script>{/literal}