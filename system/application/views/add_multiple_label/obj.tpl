<h2>Add object label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}
<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=common_label/obj.tpl}
</fieldset>
{include file=add_multiple_label/form_end.tpl}

{include file=common_label/validate/obj.tpl form=form_add_label}