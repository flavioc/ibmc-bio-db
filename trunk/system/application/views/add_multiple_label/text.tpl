<h2>Add text label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}
<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=common_label/text.tpl}
</fieldset>
{form_submit name=submit msg='Add label'}
{form_end}

{literal}
<script>
$(function () {
$("#form_add_label").validateTextLabel();
});
</script>
{/literal}
