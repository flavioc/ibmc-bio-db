{if $total > 0}
<p>The rank {$rank} is associated with {$total} taxonomies. If deleted, those taxonomies will have an empty rank.</p>
{else}
<p>Te rank {$rank} has no associated taxonomies.</p>
{/if}

{if $child}
<p>The rank {$rank} is parent of the rank {$child}, if deleted, the child will no longer have a parent.</p>
{/if}

{if $total > 0 || $child}
<p>Do you still want to delete the rank?</p>
{else}
<p>Do you want to delete it?</p>
{/if}
