<h2>Delete results</h2>

<p>You can delete the search results by clicking the button. <span class="warning">The action is irreversible</em>.</p>

{form_open to='search/do_delete_results' name=delete_form}
{form_hidden name=encoded_tree value=$encoded_no_slashes}
{form_hidden name=transform value=$transform}
{form_submit name=submit msg='Please delete the sequences below' id='submit_delete'}
{form_end}

<h3>Search term</h3>

<p id="search_tree_string">{$tree_str}</p>

{include file=search/operation_sequences.tpl dom_id=input_list}
