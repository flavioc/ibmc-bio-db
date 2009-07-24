<h2>Sequence list</h2>

{literal}
<script>
$(function () {
  var show_seqs = $('#show_sequences');
  activate_sequence_search(show_seqs);
  start_sequence_grid(show_seqs);
});
</script>
{/literal}

{include file=sequence/form_search.tpl}

<div id="show_sequences"></div>

{button name="add_seq" msg="Add new" to="sequence/add"}
{button name="export_seqs" msg="Export sequences" to="sequence/export_all"}

