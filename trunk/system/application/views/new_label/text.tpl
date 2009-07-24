<h2>New text label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="add_labels/add"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/text.tpl}
</fieldset>
{include file=common_label/form_add.tpl}

{include file=common_label/validate/text.tpl form=form_add_label}