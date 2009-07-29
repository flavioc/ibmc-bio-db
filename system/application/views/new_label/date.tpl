<h2>New date label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="add_labels/add"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/date.tpl}
</fieldset>
{include file=common_label/form_add.tpl}

{include file=common_label/validate/date.tpl form=form_add_label}