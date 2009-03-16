<h2>Browse taxonomies</h2>

<script>
{literal}
$(document).ready(function () {
  start_tax_search_form('#show_data', true);
});
{/literal}
</script>

<p>
{include file=taxonomy/form_search.tpl}
</p>

<div id="show_data"></div>

