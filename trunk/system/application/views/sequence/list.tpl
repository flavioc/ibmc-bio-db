<h2>Sequence list</h2>
<p>
<table class="data">
  <tr>
    <th>Name</th>
    <th>Type</th>
    <th>Accession Number</th>
  </tr>

{foreach from=$sequences item=sequence}
  <tr id="tr_{$sequence.id}">
    <td><a href="{site}/sequence/view/{$sequence.id}">{$sequence.name}</a></td>
    <td>{$sequence.type}</td>
    <td>{$sequence.accession}</td>
  </tr>
{/foreach}
</table>
</p>
