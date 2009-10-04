{if $label.editable && $label.multiple}
{literal}<script>
$(function () { $("#{/literal}{$form}{literal} input[name=param]").rules('add', { required: generate_disabled }); });
</script>{/literal}
{/if}