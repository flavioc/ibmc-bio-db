{include file=common_label/info.tpl}

<ul>
{if !$toadd}
<li>This label will be edited</li>
{if $label.type == 'obj'}
<li>Current object: {$label.text_data}</li>
{/if}
{if $label.type == 'ref'}
<li>Current sequence: {$label.sequence_name}</li>
{/if}
{if $label.type == 'tax'}
<li>Current taxonomy: {$label.taxonomy_name}</li>
{/if}
{else}
<li>This label will be added</li>
{/if}
</ul>

<script>
{to_js var=label value=$label}

{literal}
$(function () {
  $('#form_change_label').submitAjax();
});
{/literal}
</script>