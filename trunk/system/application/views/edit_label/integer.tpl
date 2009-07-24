<h2>Edit integer label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="edit_labels/edit"}
<fieldset>
{include file=edit_label/hidden.tpl}
{include file=common_label/integer.tpl}
</fieldset>
{form_submit name=submit msg='Edit label'}
{form_end}

{include file=common_label/validate_add/integer.tpl form=form_edit_label}