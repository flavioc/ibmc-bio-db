{include file=common_label/generate.tpl to_hide="#data_area"}
<span id="data_area">
{if $label}
{form_row type=checkbox name=boolean msg='Value:' checked=$label.bool_data}
{else}
{form_row type=checkbox name=boolean msg='Value:'}
{/if}
</span>