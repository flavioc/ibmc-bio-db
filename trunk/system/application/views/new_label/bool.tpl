
<h2>New boolean label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_bool_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide="#data_area"}
<span id="data_area">
{form_row type=checkbox name=boolean msg='Value:'}
</span>
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}
