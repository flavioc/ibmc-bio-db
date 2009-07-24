<h2>New URL label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="add_labels/add"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/url.tpl}
</fieldset>
{form_submit name=submit msg='Add label'}
{form_end}

{include file=common_label/validate_add/url.tpl}