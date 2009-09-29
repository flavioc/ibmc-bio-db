<h2>Sequence list</h2>

{literal}
<script>
$(function () {
  var show_seqs = $('#show_sequences');
  activate_sequence_search(show_seqs);
  start_sequence_grid(show_seqs);

  $('#submit_export').click(function () {
    $('input[name=export_name]').val($('#name').val());
    $('input[name=export_user]').val($('#user').val());
    
    return true;
  });
});
</script>
{/literal}

{include file=sequence/form_search.tpl}

<div id="show_sequences"></div>

{form_open to='export/export_all' name=export_form}
{form_hidden name=export_name}
{form_hidden name=export_user}
{include file=sequence/export_types.tpl}
{form_submit name=submit_export msg='Export all'}
{form_end}

{if $logged_in}
{button name="add_seq" msg="Add new" to="sequence/add"}
{/if}

{literal}
<style>
#form_add_seq, #export_form {
  display: inline;
}
</style>
{/literal}