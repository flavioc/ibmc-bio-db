<h2>Edit text label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="edit_labels/edit"}
<fieldset>
{include file=edit_label/hidden.tpl}
{include file=common_label/text.tpl}
</fieldset>
{include file=common_label/form_edit.tpl}

{include file=common_label/validate/text.tpl form=form_edit_label}