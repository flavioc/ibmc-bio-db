{if $num_seq > 0}
<p>This label is used in {$num_seq} sequences.</p>
{/if}

{if $label.default}
<p>THIS LABEL IS A SYSTEM LABEL.</p>
{/if}

{if $label.default || $num_seq > 0}
<p>Do you still want to delete the label {$label.name}?</p>
{else}
<p>Do you want to delete the label {$label.name}?</p>
{/if}
