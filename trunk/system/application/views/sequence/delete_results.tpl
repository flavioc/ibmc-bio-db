<h2>Delete results</h2>

<p>You can delete the search results by clicking the button. <em>The action is irreversible</em>.</p>

{form_open to='sequence/do_delete_results' name=delete_form}
{form_hidden name=encoded_tree value=$encoded}
{form_hidden name=transform value=$transform}
{form_hidden name=type value=$type}
{form_submit name=submit msg='Please delete the sequences below' id='submit_delete'}
{form_end}

<h3>Search term</h3>

<p id="search_tree_string">{$tree_str}</p>

{include file=sequence/operation_sequences.tpl}