<h2>Search sequences</h2>

<div id="term_form_div">
{form_open to='#' name=term_form}
<fieldset>
<p><span class="desc">Label:</span><span id="labelname">Coiso</span></p>
<div id="operator_input">
{form_row type=select name=operator msg='Operator:'}
</div>
<div id="data_input">
{form_row name=data_row msg='Data:'}
</div>
<div id="data_boolean_input">
{form_row type=checkbox name=data_boolean_checkbox msg='Data:'}
</div>
</fieldset>
{form_submit name=submit msg='Add term'}
{form_end}
</div>

{form_open to='#' name=and_form}
{form_submit name=submit msg='Add AND'}
{form_end}

{form_open to='#' name=or_form}
{form_submit name=submit msg='Add OR'}
{form_end}

<hr />

<div id="search_tree">
This is the search tree:
{form_open to='#' name=tree_form}
<ul>
</ul>
{form_end}
</div>

<hr />
<h3>Results</h3>
<p>
<div id="show_sequences"></div>
</p>

<hr />
<h3>Labels</h3>

{literal}
<script>
$(document).ready(function () {
  start_label_list({only_searchable: '1'}, label_was_selected);
});
</script>
{/literal}

{include file=label/labels_grid.tpl}
{include file=label/search_form.tpl}

