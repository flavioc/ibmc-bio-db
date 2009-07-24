<h2>New taxonomy label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="add_labels/add"}
<fieldset>
{include file=new_label/hidden.tpl}
{include file=common_label/tax_form.tpl}
</fieldset>
{form_submit name=submit_tax msg='Add label'}
{form_end}

{include file=common_label/tax.tpl form=form_add_label}

