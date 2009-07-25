<h2>Change reference label</h2>

{include file=change_label/info.tpl}

{form_open name=form_change_label to="change_labels/change"}

<fieldset>
{include file=change_label/hidden.tpl}
{include file=common_label/ref_form.tpl}
</fieldset>
{include file=common_label/form_change.tpl}

{include file="common_label/ref.tpl" form=form_change_label}