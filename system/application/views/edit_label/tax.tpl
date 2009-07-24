<h2>Edit taxonomy label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="edit_labels/edit"}
<fieldset>
{include file=edit_label/hidden.tpl}
{include file=common_label/tax_form.tpl}
</fieldset>
{include file=common_label/form_edit.tpl}

{include file=common_label/tax.tpl form=form_edit_label}