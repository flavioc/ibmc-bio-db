<h2>Edit object label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="edit_labels/edit" multipart=yes}
<fieldset>
{include file=edit_label/hidden.tpl}
{include file=common_label/obj.tpl}
</fieldset>
{include file=common_label/form_edit.tpl}

{include file=common_label/validate/obj.tpl form=form_edit_label}