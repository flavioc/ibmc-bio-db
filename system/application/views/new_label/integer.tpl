
<h2>New integer label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="sequence/add_integer_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{form_row name=integer msg='Integer:'}
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}
