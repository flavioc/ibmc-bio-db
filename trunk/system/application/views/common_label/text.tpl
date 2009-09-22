{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=text msg='Text:' value=$label.text_data}
{else}
{form_row name=text msg='Text:'}
{/if}
{include file=common_label/param.tpl}
</span>