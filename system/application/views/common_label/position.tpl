{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $label}
{form_row name=start msg="Start:" value=$label.position_a_data}
{form_row name=length msg="Length:" value=$label.position_b_data}
{else}
{form_row name=start msg="Start:"}
{form_row name=length msg="Length:"}
{/if}
</span>