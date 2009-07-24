<h2>New position label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_position_label"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/position.tpl}
</fieldset>
{form_submit name=submit_pos msg='Add label'}
{form_end}

{include file=common_label/validate_add/position.tpl}