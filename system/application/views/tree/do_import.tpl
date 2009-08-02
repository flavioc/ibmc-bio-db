<h2>Import tree results</h2>

{if $stats.mode == 'edit'}
<p>Tree <a href="{site}/tree/view/{$stats.id}">{$stats.name}</a> was already inserted.</p>
{else}
<p>Tree <a href="{site}/tree/view/{$stats.id}">{$stats.name}</a> has been created.</p>
{/if}

<ul>
  <li>{$stats.new_ranks} ranks were added.</li>
  <li>{$stats.new_tax} taxonomies were created.</li>
  <li>{$stats.old_tax} taxonomies were already present.</li>
</ul>

<p>You can <a href="{site}/taxonomy/tree_browse?start={$stats.id}">browse</a> the imported tree.</p>