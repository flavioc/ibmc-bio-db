<p>The imported sequences can be <a href="{site}/search?type=batch" id="batch_manipulate_{$what}">batch manipulated</a>.</p>

{literal}<script>
$(function () {
  $('#batch_manipulate_{/literal}{$what}{literal}').click(function () {
    $.cookie('saved_search_tree', $.toJSON({/literal}{$search_tree_get}{literal}, true), cookie_options);
    return true;
  });
})
</script>{/literal}

{if count($labels) > 0}
<h3>Labels</h3>
<table class="data">
  <tr>
    <th style="width: 20%;">Label</th>
    <th style="width: 20%;">Type</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels key=name item=label}
  <tr>
    <td class="centered">{$name}</td>
    <td class="centered">{$label.type}</td>
    <td>{if $label.status == 'not_found'}Label is not installed{/if}{if $label.status == 'type_differ'}Label types are different ({$label.new_type}){/if}{if $label.status == 'ok'}OK{/if}</td>
  </tr>
  {/foreach}
</table>
<br />
{/if}

{foreach from=$sequences item=seq}
<h4>Sequence</h4>
<table class="data">
  <tr>
    <th style="width: 10%;">New</th>
    <th style="width: 20%;">Name</th>
    <th style="width: 30%;">Content</th>
    {if $seq.comment}
    <th>Comment</th>
    {/if}
  </tr>
  <tr>
    <td class="centered">{boolean value=$seq.add}</td>
    <td><a href="{site}/sequence/view/{$seq.id}">{$seq.name}</a></td>
    <td class="centered">{$seq.short_content}...</td>
    {if $seq.comment}
    <td>{$seq.comment}</td>
    {/if}
  </tr>
</table>
<br />

{if count($labels) > 0 && $seq.labels}
{assign var=labelsseq value=$seq.labels}
<table class="data">
  <tr>
    <th style="width: 20%;">Label</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels key=name item=label}
  <tr>
    <td>{$name}</td>
    <td>{$labelsseq[$name].status}</td>
  </tr>
  {/foreach}
</table>
<br />
{/if}

{/foreach}
