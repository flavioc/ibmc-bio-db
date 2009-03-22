
<h2>Edit taxonomy label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_tax_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide="#data_area, #taxonomy_search"}
<span id="data_area">
{form_row name=tax msg="Taxonomy:" readonly=readonly}
{form_hidden name=hidden_tax}
</span>
</fieldset>

{form_submit name=submit_tax msg='Edit label'}
{form_end}

{include file=common_label/tax.tpl form=form_edit_label}

