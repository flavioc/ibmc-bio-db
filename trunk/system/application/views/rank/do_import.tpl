<h2>Import ranks report</h2>

{if $ranks}
<p>The next table shows the import results:</p>
{include file=rank/common_import.tpl}
{else}
<p>No ranks were imported.</p>
{/if}