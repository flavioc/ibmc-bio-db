
<h2>New text label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="sequence/add_text_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{form_row name=text msg='Text:'}
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}
