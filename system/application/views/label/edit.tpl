<h2>Edit label <a href="{site}/label/view/{$label.id}">{$label.name}</a></h2>

<script>
validate_label_form('#edit_form');
</script>

{assign var=label_id value=$label.id}

{if !$label.default}

{form_open to="label/do_edit/$label_id" name=edit_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$types name=type msg='Type:' start=$label.type key=name}
{form_row type=checkbox name=autoadd msg='Auto Add:' checked=$label.autoadd}
{form_row type=checkbox name=mustexist msg='Must Exist:' checked=$label.must_exist}
{form_row type=checkbox name=auto_on_creation msg='Generate on creation' checked=$label.auto_on_creation}
{form_row type=checkbox name=auto_on_modification msg='Generate on modification' checked=$label.auto_on_modification}
<br />

{form_row type=checkbox name=deletable msg='Deletable:' checked=$label.deletable}
{form_row type=checkbox name=editable msg='Editable:' checked=$label.editable}
{form_row type=checkbox name=multiple msg='Multiple:' checked=$label.multiple}
{form_row type=textarea name=code msg='Code:' cols=50 rows=5 value=$label.code}
{form_row type=textarea name=comment msg='Comment:' cols=50 rows=5 value=$label.comment}

</fieldset>
{form_submit name=submit msg='Do edit'}
{form_end}

{else}
<p>The label <strong>{$label.name}</strong> is not editable.</p>
{/if}
