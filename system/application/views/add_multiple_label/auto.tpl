{if $mode == 'add'}
<h2>Add Generated label</h2>
{elseif $mode == 'edit'}
<h2>Edit Generated label</h2>
{/if}

<p>This label will be auto generated for each sequence</p>

{include file=add_multiple_label/info.tpl}

{form_open name=form_add_label to="multiple_labels/add_auto_label"}
{include file=add_multiple_label/hidden.tpl}
{form_submit name=submit msg='Generate label'}
{form_end}