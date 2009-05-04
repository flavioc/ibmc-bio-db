<h2>Label list</h2>

{literal}
<script>
$(document).ready(function () {
  start_label_list();
});
</script>
{/literal}

{include file=label/search_form.tpl}
{include file=label/labels_grid.tpl}
{button name="add_label" msg="Add new" to="label/add"}
