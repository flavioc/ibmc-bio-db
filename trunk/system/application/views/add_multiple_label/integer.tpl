<h2>Add integer label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}

<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=common_label/integer.tpl}
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}

{include file=common_label/validate_add/integer.tpl}
