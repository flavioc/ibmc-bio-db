<h2>New reference label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_ref_label"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/ref_form.tpl}
</fieldset>
{form_submit name=submit_file msg='Add label'}
{form_end}

{include file="common_label/ref.tpl" form=form_add_label}

