<h2>Add label</h2>

<script>
validate_label_form('#add_form');
</script>

{form_open to='label/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$types name=type msg='Type:' key=name}
{form_row type=checkbox name=autoadd msg='Auto add:'}
{form_row type=checkbox name=mustexist msg='Must exist:'}
{form_row type=checkbox name=auto_on_creation msg='Generate on creation:'}
{form_row type=checkbox name=auto_on_modification msg='Generate on modification:'}<br />
{form_row type=checkbox name=deletable msg='Deletable:'}
{form_row type=checkbox name=editable msg='Editable:'}
{form_row type=checkbox name=multiple msg='Multiple:'}
{form_row type=checkbox name=default msg='Default:'}
{form_row type=checkbox name=public msg='Public:'}
{form_row type=textarea name=code msg='Code:' cols=50 rows=5}
{form_row type=textarea name=valid_code msg='Validation code:' cols=50 rows=5}
{form_row type=textarea name=comment msg='Comment:' cols=50 rows=5}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}

