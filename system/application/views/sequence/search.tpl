{if $type == 'dna'}
<h2>Search DNA sequences</h2>
{elseif $type == 'protein'}
<h2>Search protein sequences</h2>
{else}
<h2>Search sequences</h2>
{/if}

<div id="insert_terms">
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
  <div id="data_position_input">
    <span id="position_type_text" {display_none}></span>
    <select id="position_type">
      <option value="start">start</option>
      <option value="length">length</option>
    </select>
  </div>
  <div id="data_input">
    <input id="data_row" type="text" value="" name="data_row" />
  </div>
  <div id="data_boolean_input">
    <input id="data_boolean_checkbox" type="checkbox" value="yes" name="data_boolean_checkbox" />
  </div>
  <div id="data_tax_input">
    <span id="data_tax"></span>
    <span id="change_tax">(Find taxonomy)</span>
  </div>
  <div id="data_seq_input">
    <span id="data_seq"></span>
    <span id="change_seq">(Find sequence)</span>
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

{form_open to='#' name=not_form}
{form_submit name=submit msg='Add NOT'}
{form_end}

<div id="search_tree">
<ol class="search-list" level="1">
</ol>
</div>

</div>

{form_open to='sequence/export_search' name=tree_form}
{form_hidden name=encoded_tree value=null}
{form_submit name=submit msg='Export' id="submit_tree"}
{form_end}

{form_open to='sequence/multiple_add_label?mode=add' name=add_label_form}
{form_hidden name=encoded_tree value=null}
{form_submit name=submit msg='Add label' id='submit_add_label'}
{form_end}

{form_open to='sequence/multiple_add_label?mode=edit' name=edit_label_form}
{form_hidden name=encoded_tree value=null}
{form_submit name=submit msg='Edit label' id='submit_edit_label'}
{form_end}

<h3>Results</h3>
<p>
<div id="show_sequences"></div>
</p>
