<h2>Add object label</h2>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_label"}
<fieldset>
{include file=add_multiple_label/hidden.tpl}
{include file=common_label/obj.tpl}
</fieldset>
{form_submit name=submit_file msg='Add label'}
{form_end}

{include file=common_label/validate_add/obj.tpl}