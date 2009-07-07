<div id="seq_search">
</div>

{literal}
<script>
$(document).ready(function () {
  var place = $('#seq_search');
  start_sequence_grid(place, {
      name: function (row) {
        place.gridHighLight(row.id);
        $('#data_seq').text(row.name);
        $('#data_seq')[0].seq = row;
        tb_remove();
      }
  });
});
</script>
{/literal}

