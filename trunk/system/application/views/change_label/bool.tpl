<h2>Change boolean label</h2>

{include file=change_label/info.tpl}

{form_open name=form_change_label to="change_labels/change"}
<fieldset>
{include file=change_label/hidden.tpl}
{include file=common_label/bool.tpl}
</fieldset>
{include file=common_label/form_change.tpl}