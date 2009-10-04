<h2>Rank list</h2>

{literal}
<script>
$(function () {
  var base_site = get_app_url() + '/rank/';
  var show_ranks = $('#show_ranks');
  var fieldNames = ['Name', 'Parent', 'Last update', 'User'];
  var fields = ['rank_name', 'rank_parent_name', 'update', 'user'];
  var name_field = $('#name');
  var parent_field = $('#parent_name');
  var user_field = $('#user');
  
  if(get_logged_in()) {
    fieldNames.push('Taxonomy');
    fieldNames.push('Child');
    fields.push('add');
    fields.push('add_child');
  }

  show_ranks
  .gridEnable()
  .grid({
    url: get_app_url() + '/rank',
    retrieve: 'get_all',
    fieldNames: fieldNames,
    fields: fields,
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
    idField: 'rank_id',
    params: {
      name: function () { return name_field.val(); },
      parent_name: function () { return parent_field.val(); },
      user: function () { return user_field.val(); }
    }
  });

  function when_submit()
  {
    show_ranks.gridReload();
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

{if $logged_in}
  {button name="add_rank" to="rank/add" msg="Add rank"}
{/if}

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