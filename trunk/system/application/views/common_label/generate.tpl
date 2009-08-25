{if $label.code}
{form_row type=checkbox name=generate_check msg='Generate default value:'}
{literal}<script>
$(function () { $('{/literal}{$to_hide}{literal}').activateGenerate(); });
</script>{/literal}
{/if}