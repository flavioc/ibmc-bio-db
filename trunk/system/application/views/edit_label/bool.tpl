
<h2>Edit boolean label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_bool_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide="#data_area"}
<span id="data_area">
{form_row type=checkbox name=boolean msg='Boolean:' checked=$label.bool_data}
</span>
</fieldset>

{form_submit name=submit msg='Edit label'}
{form_end}
