<h2>Edit position label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="edit_labels/edit"}
<fieldset>
{include file=edit_label/hidden.tpl}
{include file=common_label/position.tpl}
</fieldset>
{form_submit name=submit_pos msg='Edit label'}
{form_end}

{include file=common_label/validate/position.tpl form=form_edit_label}