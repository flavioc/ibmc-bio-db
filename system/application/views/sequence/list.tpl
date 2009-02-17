<h2>Sequence list</h2>

{literal}
<script>
$(document).ready(function () {

  $('#show_sequences')
  .gridEnable({paginate: false})
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Type', 'Accession Number'],
    fields: ['name', 'type', 'accession'],
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      }
    }
  });

});
</script>
{/literal}

<p>
<div id="show_sequences"></div>
</p>
