{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=url msg='URL:' value=$label.url_data}
{else}
{form_row name=url msg='URL:'}
{/if}
</span>