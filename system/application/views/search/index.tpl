{if $type == 'dna'}
<h2>Search DNA sequences</h2>
{elseif $type == 'protein'}
<h2>Search protein sequences</h2>
{elseif $type == 'batch'}
<h2>Search batch sequences</h2>
{elseif $type == 'search'}
<h2>Search sequences</h2>

{literal}<script>
$(function () {
  // focus search input
  {/literal}{if $type == 'search'}
  $('#form_search_global input[type=text]').effect('highlight', {literal}{color: 'red'}{/literal}, 3000);
  {/if}{literal}
  $('#form_search_global input[type=text]').focus();
});
</script>{/literal}

{elseif $type == 'label' || $type == 'notlabel'}
  {if $label}
    <h2>Search sequences by label {$label.name}</h2>
  {else}
    <h2>Search sequences by label</h2>
  {/if}
{else}
<h2>Search sequences</h2>
{/if}

<div id="insert_terms">
<div id="term_form_div">
{form_open to='#' name=term_form}
<fieldset>
<label for="label_row" class="search-desc">Label</label><input id="label_row" type="text" value="" name="label_row" /><span id="label_name" {display_none}></span>

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
    <input id="data_row" type="text" value="" name="data_row" size="10"/>
  </div>
  <div id="data_boolean_input">
    <input id="data_boolean_checkbox" type="checkbox" value="yes" name="data_boolean_checkbox" />
  </div>
  <div id="data_tax_input">
    <span id="data_tax"></span>
    <span id="change_tax">(Find taxonomy)</span>
  </div>
  <div id="data_date_input">
    {form_input name=date_input readonly=yes}
  </div>
  <div id="data_seq_input">
    <span id="data_seq"></span>
    <span id="change_seq">(Find sequence)</span>
  </div>
  <div id="param_input">
    <label for="label_row" class="data_param">Param </label>
    <input id="data_param" type="text" value="" name="data_param" size="5"/>
  </div>
</div>

{form_submit name=submit msg='Add term' id="submit_term"}
</fieldset>
{form_end}
</div>

<div id="operator_box">
{form_open to='#' name=and_form}
{form_submit msg='Add AND'}
{form_end}

{form_open to='#' name=or_form}
{form_submit msg='Add OR'}
{form_end}

{form_open to='#' name=not_form}
{form_submit msg='Add NOT'}
{form_end}

{form_open to='#' name=reset_form}
{form_submit msg='Reset'}
{form_end}
</div>

{literal}
<script>
$(function () {
  $('#show_tree').minusPlus({
    enabled: false,
    plusEnabled: function () {
      $('#search_tree').slideDown();
    },
    minusEnabled: function () {
      $('#search_tree').slideUp();
    }
  });
});
</script>
{/literal}

<div id="search_box">
  <div id="show_tree"></div>
  <div id="search_tree" {display_none}>
  <ol class="search-list" level="1">
  </ol>
  </div>
</div>

<div id="search_human">
</div>

<div id="search_sql" {display_none}>
</div>

</div>

<div id="transform_box">
  <label for="select_transform" class="search-desc">Transform results: </label>
  {form_select blank=yes name=select_transform start=0 data=$refs key=id}
</div>

<h3>Preview</h3>
<div id="show_result_list" {display_none}>
  <div id="label_box">
    {form_open to='#' name=add_label_form}
    <label for="label_result" class="search-desc">View label: </label>
    {form_input name=label_result size=20}
    {form_submit msg='View' id='add_label_button'}
    {form_end}
  </div>
</div>
<div id="show_result_total">
{loader_pic id=total_loader show=no}
<p id="total_num"></p>
<a class="view_full" href='#view_full_list'>(View full list)</a>
</div>

<div id="show_sequences"></div>

<h3>Operations</h3>

<div id="operations_box">
{form_open to='export/export_search' name=tree_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='Export' id="submit_tree"}
{form_end}

{if $logged_in}
{form_open to='blast/blast_form' name=blast_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='BLAST' id='submit_blast'}
{form_end}

{form_open to='search/add_label?mode=add' name=add_label_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='Add label' id='submit_add_label'}
{form_end}

{form_open to='search/add_label?mode=edit' name=edit_label_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='Edit label' id='submit_edit_label'}
{form_end}

{form_open to='search/delete_label' name=delete_label_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='Delete label' id='submit_delete_label'}
{form_end}

{form_open to='search/delete_results' name=delete_results_form}
{form_hidden name=encoded_tree value=null}
{form_hidden name=transform_hidden value=null}
{form_submit name=submit msg='Delete results' id='submit_delete_results'}
{form_end}

{/if}

{if $logged_in}
<div id="generate_sequences_box">
  {form_open to="search/subsequences" name=generate_sequences_form}
  <label for="select_position" class="search-desc">Generate subsequences: </label>
  {form_hidden name=encoded_tree value=null}
  {form_hidden name=transform_hidden value=null}
  {form_select name=select_position data=$positions key=id}
  {form_checkbox name=keep_subsequence} Keep
  {form_submit msg="Generate" id="generate_sequences_button"}
  {form_end}
</div>
{/if}
<div id="generate_histogram_box">
  {form_open to='search/get_histogram' name=generate_histogram_form}
    {form_hidden name=encoded_tree value=null}
    {form_hidden name=transform_hidden value=null}
    {form_hidden name=histogram_label value=null}
    <label for="generate_histogram" class="search-desc">Generate histogram: </label>
    {form_input name=generate_label size=20}
    <select name="generate_histogram_type" id="generate_histogram_type">
      <option value="avg">avg</option>
      <option value="max">max</option>
      <option value="min">min</option>
    </select>
    {form_submit msg='Show' id='show_histogram_button'}
  {form_end}
</div>

</div>

<!-- Needed for the histogram thickbox -->
<div id="histogram_data" {display_none}>
</div>

{include file=loading.tpl}