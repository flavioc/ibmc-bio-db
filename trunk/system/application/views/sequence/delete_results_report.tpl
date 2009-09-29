<h2>Delete report</h2>

<p>A total of {$total} sequences were deleted.</p>

<p id="search_tree_string">{$tree_str}</p>

{form_open method=get to='sequence/search' name=back_form}
{form_hidden name=type value=$type}
{form_submit msg='Search again'}
{form_end}