{if $total > 0}
<p>The tree {$tree} contains {$total} taxonomies. If deleted, those taxonomies will also be deleted.</p>
{else}
<p>The tree {$tree} has no taxonomies.</p>
{/if}

{if $total > 0}
<p>Do you still want to delete the tree?</p>
{else}
<p>Do you want to delete it?</p>
{/if}
