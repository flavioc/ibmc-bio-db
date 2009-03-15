<h2>Sequence list</h2>

{literal}
<script>
$(document).ready(function () {

  var paging_size = {/literal}{$paging_size}{literal};

  $('#show_sequences')
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    total: 'get_total',
    size: paging_size,
    fieldNames: ['Name', 'Type', 'Accession Number', 'Last update', 'User'],
    fields: ['name', 'type', 'accession', 'update', 'user_name'],
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
