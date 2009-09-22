{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=float msg='Float:' value=$label.float_data}
{else}
{form_row name=float msg='Float:'}
{/if}
{include file=common_label/param.tpl}
</span>