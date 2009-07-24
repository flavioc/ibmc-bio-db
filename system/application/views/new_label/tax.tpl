<h2>New taxonomy label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_tax_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide="#data_area, #taxonomy_search"}
<span id="data_area">
{form_row name=tax msg="Taxonomy:" readonly=readonly}
{form_hidden name=hidden_tax}
</span>
</fieldset>

{form_submit name=submit_tax msg='Add label'}
{form_end}

{include file=common_label/tax.tpl form=form_add_label}

