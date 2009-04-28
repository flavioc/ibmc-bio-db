{if $num_children > 0}
<p>This taxonomy has {$num_children} children.</p>
{/if}
{if $num_labels > 0}
<p>This taxonomy is associated with {$num_labels} labels.</p>
{/if}

{if $num_labels > 0 || $num_children > 0}
<p>Do you still want to delete this taxonomy?</p>
{else}
<p>Do you want to delete this taxonomy?</p>
{/if}
