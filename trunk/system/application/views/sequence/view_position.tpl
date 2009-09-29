<h3>Sequence segment: starting from {$start} with length {$length}</h3>

{if $invalid}
<p class="sequence_segment_error">The segment is invalid as the sequence length is {$actual_length}.</p>
{else}
<p class="sequence_segment">{$segment}</p>
{/if}