<h2>Distribution results for label <em>{$label.name}</em></h2>

<div id="histogram"></div>

{literal}<script>
$(function () {
  $('#histogram').plot({/literal}{$hist_data}{literal}, {width: 600});
});
</script>{/literal}

<ul>
  <li>Total: {$total}</li>
  <li>Classes: {$number_classes}</li>
  {if $label.type == 'integer' || $label.type == 'float'}
  <li>Smallest class: {$min_class}</li>
  <li>Largest class: {$max_class}</li>
  <li>Average: {$average}</li>
  <li>Median: {$median}</li>
  {/if}
  <li>Mode: {$mode}</li>
</ul>