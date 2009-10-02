<h2>Distribution results for label <em>{$label.name}</em></h2>

{if $empty}

<p>No results were found.</p>

{else}

<div id="histogram"></div>

{literal}<script>
$(function () {
  $('#histogram').plot({/literal}{$hist_data}{literal}, {width: 600});
});
</script>{/literal}

<br />
<table class="data" style="width: 625px;">
  <tr>
    <th style="width: 20%;">Type</th>
    <th>Value</th>
  </tr>
  <tr>
    <td>Total</td>
    <td>{$total}</td>
  </tr>
  <tr>
    <td>Classes</td>
    <td>{$number_classes}</td>
  </tr>
  {if $label.type == 'integer' || $label.type == 'float'}
  <tr>
    <td>Smallest class</td>
    <td>{$min_class}</td>
  </tr>
  <tr>
    <td>Largest class</td>
    <td>{$max_class}</td>
  </tr>
  <tr>
    <td>Average</td>
    <td>{$average}</td>
  </tr>
  <tr>
    <td>Median</td>
    <td>{$median}</td>
  </tr>
  {/if}
  <tr>
    <td>Mode</td>
    <td>{split_value val=$mode}</td>
  </tr>
</table>

{/if}