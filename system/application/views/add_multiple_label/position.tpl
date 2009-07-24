<h2>Add position label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}
<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=common_label/position.tpl}
</fieldset>
{include file=common_label/form_add.tpl}

{include file=common_label/validate/position.tpl form=form_add_label}