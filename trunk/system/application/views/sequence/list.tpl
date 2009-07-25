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

{form_open to='sequence/export_all' name=export_form}
{include file=sequence/export_types.tpl}
{form_submit name=submit msg='Export all'}
{form_end}