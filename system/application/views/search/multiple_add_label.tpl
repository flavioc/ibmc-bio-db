{include file=loading.tpl}

{if $mode == 'add'}
<h2>Add label to multiple sequences</h2>
{elseif $mode == 'edit'}
<h2>Edit label in multiple sequences</h2>
{/if}

{form_open to='#' name=label_form}
{form_hidden name=search value=$encoded_no_slashes}
{form_hidden name=transform value=$transform}

{include file=common_label/select_label.tpl}

{if $mode == 'add'}
{form_row type=checkbox name=update msg="Update:"}
{/if}

{if $mode == 'edit'}
{form_row type=checkbox name=addnew msg='Add new:'}
{/if}

{form_submit name='submit_add_label' msg='Next...'}
{form_end}

<div id="info_results"></div>

<h3>Search term</h3>

<p id="search_tree_string">{$tree_str}</p>

<h3>Sequences</h3>

{include file=search/operation_sequences.tpl add_change=yes dom_id=input_list}

<div id="loading-labels" class="loading-div" {display_none}>
{loader_pic show=yes}
<div>
</div>
</div>