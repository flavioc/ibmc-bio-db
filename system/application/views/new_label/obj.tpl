
<h2>New object label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="sequence/add_obj_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{form_row name=file msg="File:" type=upload}
</fieldset>

{form_submit name=submit_file msg='Add label'}
{form_end}
