<h2>New text label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_text_label"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/text.tpl}
</fieldset>
{form_submit name=submit msg='Add label'}
{form_end}

{include file=common_label/validate_add/text.tpl}