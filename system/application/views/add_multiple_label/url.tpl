<h2>New URL label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}

<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=url msg='URL:'}
</span>
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}

{literal}
<script>
$(function () {
	$("#form_add_label").validateUrlLabel();
});
</script>
{/literal}