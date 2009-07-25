<h2>Change integer label</h2>

{include file=change_label/info.tpl}

{form_open name=form_change_label to="change_labels/change"}
<fieldset>
{include file=change_label/hidden.tpl}
{include file=common_label/integer.tpl}
</fieldset>
{include file=common_label/form_change.tpl}

{include file=common_label/validate/integer.tpl form=form_change_label}