{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=date msg='Date:' value=$label.date_data readonly=yes}
{else}
{form_row name=date msg='Date:' readonly=yes}
{/if}
{include file=common_label/param.tpl}
</span>