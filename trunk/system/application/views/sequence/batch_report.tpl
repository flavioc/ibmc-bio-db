<h2>Batch results</h2>

<p>The following sequences marked 'Yes' in 'New' were imported:</p>

<table class="data">
  <tr>
    <th>New</th>
    <th>Name</th>
    <th>Content</th>
  </tr>
  {foreach from=$sequences item=seq}
  <tr>
    <td>{boolean value=$seq.add}</td>
    <td><a href="{site}/sequence/view/{$seq.id}">{$seq.name}</a></td>
    <td>{$seq.short_content}...</td>
  </tr>
  {/foreach}
</table>

