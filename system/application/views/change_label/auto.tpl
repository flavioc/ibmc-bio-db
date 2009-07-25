<h2>Change Generated label</h2>

{include file=change_label/info.tpl}

{form_open name=form_change_label to="change_labels/auto_change"}
{include file=change_label/hidden.tpl}
{form_submit name=submit msg='Generate label'}
{form_end}