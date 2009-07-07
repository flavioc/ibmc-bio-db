<h2>Search sequences</h2>

<div id="term_form_div">
{form_open to='#' name=term_form}
<fieldset>
<label for="label_row" class="search-desc">Label </label>
<input id="label_row" type="text" value="" name="label_row" />
<span id="label_name" {display_none}></span>

<div id="term_other_fields" {display_none} >
  <div id="operator_input">
    <span id="operator_text" {display_none}></span>
    {form_select name=operator class="search-operator"}
  </div>
  <div id="data_input">
    <input id="data_row" type="text" value="" name="data_row" />
  </div>
  <div id="data_boolean_input">
    <input id="data_boolean_checkbox" type="checkbox" value="yes" name="data_boolean_checkbox" />
  </div>
</div>

{form_submit name=submit msg='Add term' id="submit_term"}
</fieldset>
{form_end}
</div>


{form_open to='#' name=and_form}
{form_submit name=submit msg='Add AND'}
{form_end}

{form_open to='#' name=or_form}
{form_submit name=submit msg='Add OR'}
{form_end}

<div id="search_tree">
{form_open to='#' name=tree_form}
<ol class="search-list" level="1">
</ol>
{form_end}
</div>

<h3>Results</h3>
<p>
<div id="show_sequences"></div>
</p>
