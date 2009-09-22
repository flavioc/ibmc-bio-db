{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=integer msg='Integer:' value=$label.int_data}
{else}
{form_row name=integer msg='Integer:'}
{/if}
{include file=common_label/param.tpl}
</span>