<h2>Batch results</h2>

<p>The following sequences marked 'Yes' in 'New' were imported:</p>
<p><strong>{$file}</strong></p>

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

{foreach from=$sequences item=seq}
{assign var=data value=$seq.data}
<h3>Sequences</h3>
<table class="data">
  <tr>
    <th>New</th>
    <th>Name</th>
    <th>Content</th>
    <th>Comment</th>
  </tr>
  <tr>
    <td class="centered" style="width: 7%;">{boolean value=$seq.add}</td>
    <td><a href="{site}/sequence/view/{$data.id}">{$data.name}</a></td>
    <td class="centered" style="width: 25%;">{$seq.short_content}...</td>
    <td>{$seq.comment}</td>
  </tr>
</table>
<br />

<table class="data">
  <tr>
    <th>Label</th>
    <th>Status</th>
  </tr>
  {assign var=labelseq value=$seq.labels}
  {foreach from=$labels item=label}
  <tr>
    <td>{$label.name}</td>
    <td>{$labelseq[$label.name]}</td>
  </tr>
  {/foreach}
</table>
<br />
{/foreach}

