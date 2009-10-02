<h2>Batch results</h2>

<p>The following sequences marked 'Yes' in 'New' were inserted into the database, the others were updated.</p>

{if $sequences2}
<h3>DNA file</h3>
{/if}

{include file=sequence/batch_report_single.tpl what=first labels=$labels1 sequences=$sequences1 search_tree_get=$search_tree_get1}

{if $sequences2}
<h3>Protein file</h3>

{include file=sequence/batch_report_single.tpl what=second labels=$labels1 sequences=$sequences2 search_tree_get=$search_tree_get2}
{/if}