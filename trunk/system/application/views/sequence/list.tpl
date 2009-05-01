<h2>Sequence list</h2>

{literal}
<script>
$(document).ready(function () {
  var changed = false;
  var show_seqs = $('#show_sequences');
  var name_field = $('#name');
  var user_field = $('#user');

  function changed_function ()
  {
    changed = true;
  }

  function when_submit()
  {
    if(changed) {
      var name_val = name_field.val();
      var user_val = user_field.val();

      show_seqs.gridColumnFilter('name', name_val);
      show_seqs.gridColumnFilter('user', user_val);
      show_seqs.gridReload();
    }

    changed = false;
  }

  name_field.change(changed_function);
  user_field.change(changed_function);

  $("#form_search").validate({
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  show_seqs
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    total: 'get_total',
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    tdClass: {user_name: 'centered', update: 'centered'},
    width: {
      user_name: w_user,
      update: w_update
    },
    ordering: {
      name: 'asc',
      update: 'def',
      user_name: 'def'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    }
  });

});
</script>
{/literal}

{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row type=select data=$users name=user msg='User:' key=id blank=yes}
{form_submit name=submit_search msg=Filter}
{form_end}

<p>
<div id="show_sequences"></div>
</p>

{button name="add_seq" msg="Add new" to="sequence/add"}
{button name="export_seqs" msg="Export sequences" to="sequence/export_all"}

