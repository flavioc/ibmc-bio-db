{if $label.editable && $label.multiple}
{if $label.param}
{form_row name=param msg='Param:' value=$label.param}
{else}
{form_row name=param msg='Param:'}
{/if}
{/if}