{if $total > 0}
<p>The rank {$rank} is associated with {$total} taxonomies. If deleted, those taxonomies will also be deleted.</p>
{else}
<p>Te rank {$rank} has no associated taxonomies.</p>
{/if}

{if $total_children > 0}
<p>The rank {$rank} is parent of the following {$total_children} ranks:{foreach from=$children item=child} {$child}{/foreach}.
if deleted, these children will no longer have a parent.</p>
{/if}

{if $total > 0 || $total_children > 0}
<p>Do you still want to delete the rank?</p>
{else}
<p>Do you want to delete it?</p>
{/if}
