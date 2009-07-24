<h2>New object label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="add_labels/add"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/obj.tpl}
</fieldset>
{form_submit name=submit_file msg='Add label'}
{form_end}

{include file=common_label/validate_add/obj.tpl}