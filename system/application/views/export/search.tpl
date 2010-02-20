<h2>Export search</h2>

<script>
{literal}
$(function () {
  $('#add_form').submit(function () {
    var data = [];

    $('input[type=checkbox]', $(this)).each(function () {
      var $this = $(this);
      var checked = $this.attr('checked');

      if(checked) {
        data.push($this.attr('value'));
      }
    });

    var enc = $.toJSON(data);

    $('input[name=label_obj]').val(enc);

    return true;
  });
});
{/literal}
</script>

{form_open to='export/get_export' name=add_form}
<fieldset>
{form_hidden name=tree value=$tree_json}
{form_hidden name=transform value=$transform}
{form_hidden name=label_obj value=""}
<ul>
{foreach from=$labels item=label}
  <li>{form_checkbox name=label checked=$label.intersection value=$label.id} {$label.name} ({$label.type})</li>
{/foreach}
</ul>
</fieldset>
{include file=export/types.tpl csv=true}
{form_submit name=submit msg='Download file'}
{form_end}

<h3>Search term</h3>

<p id="search_tree_string">{$tree_str}</p>

{include file=search/operation_sequences.tpl dom_id=result_list}