<h2>Batch results</h2>

<p>The following sequences marked 'Yes' in 'New' were imported:</p>

{if count($labels) > 0}
<h3>Labels</h3>
<table class="data">
  <tr>
    <th>Label</th>
    <th>Type</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels item=label}
  <tr>
    <td class="centered">{$label.name}</td>
    <td class="centered">{$label.type}</td>
    <td>{if $label.status == 'not_found'}Label is not installed{/if}{if $label.status == 'type_differ'}Label types are different ({$label.new_type}){/if}{if $label.status == 'ok'}OK{/if}</td>
  </tr>
  {/foreach}
</table>
<br />
{/if}

{foreach from=$sequences item=seq}
{assign var=data value=$seq.data}
<h4>Sequence</h4>
<table class="data">
  <tr>
    <th>New</th>
    <th>Name</th>
    <th>Content</th>
    {if $seq.comment}
    <th>Comment</th>
    {/if}
  </tr>
  <tr>
    <td class="centered" style="width: 7%;">{boolean value=$seq.add}</td>
    <td><a href="{site}/sequence/view/{$data.id}">{$data.name}</a></td>
    <td class="centered" style="width: 25%;">{$seq.short_content}...</td>
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
    <th>Label</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels item=label}
  {assign var=labelname value=$label.name}
  <tr>
    <td>{$label.name}</td>
    <td>{$labelsseq[$labelname].status}</td>
  </tr>
  {/foreach}
</table>
<br />
{/if}
{/foreach}
