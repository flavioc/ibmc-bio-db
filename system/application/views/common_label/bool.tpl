{include file=common_label/generate.tpl to_hide="#data_area"}
<span id="data_area">
{if $label}
{form_row type=checkbox name=boolean msg='Yes?:' checked=$label.bool_data}
{else}
{form_row type=checkbox name=boolean msg='Yes?:'}
{/if}
{include file=common_label/param.tpl}
</span>