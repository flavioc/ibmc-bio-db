{include file=loading.tpl}

<h2>Delete label from multiple sequences</h2>

{literal}
<style>
#submit_delete_label {
  display: block;
}

#info_results {
  margin-top: 10px;
}
</style>
{/literal}

{form_open to='#' name=label_form}
{form_hidden name=search value=$encoded}
{form_hidden name=transform value=$transform}
{include file=common_label/select_label.tpl}

{form_submit name='submit_delete_label' msg='Next...'}
{form_end}

<div id="info_results"></div>

<h3>Sequences</h3>

{include file=sequence/operation_sequences.tpl}