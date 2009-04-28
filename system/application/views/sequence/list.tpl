<h2>Sequence list</h2>

{literal}
<script>
$(document).ready(function () {

  $('#show_sequences')
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

<p>
<div id="show_sequences"></div>
</p>

{button name="add_seq" msg="Add new" to="sequence/add"}
