<h2>Browse taxonomies</h2>

<script>
{literal}
$(document).ready(function () {
  var paging_size = {/literal}{$paging_size}{literal};

  start_tax_search_form('#show_data', paging_size, true);
});
{/literal}
</script>

<p>
{include file=taxonomy/form_search.tpl}
</p>

<div id="show_data"></div>

