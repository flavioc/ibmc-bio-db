<h2>{$type} batch results</h2>

<p>The following sequences marked 'Yes' in 'New' were inserted into the database, the others were updated:</p>

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

{foreach from=$sequences key=name item=seq}
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
    <td><a href="{site}/sequence/view/{$seq.id}">{$name}</a></td>
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
