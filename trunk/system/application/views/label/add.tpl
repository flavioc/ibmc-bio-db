<h2>Add label</h2>

<script>
validate_label_form('#add_form');
</script>

{form_open to='label/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$types name=type msg='Type:' key=name}
{form_row type=textarea name=code msg='Code:' cols=50 rows=5}
{form_row type=textarea name=valid_code msg='Validation code:' cols=50 rows=5}
{form_row type=textarea name=action_modification msg='Modification code:' cols=50 rows=5}
{form_row type=textarea name=comment msg='Comment:' cols=50 rows=5}

<div class="grid_form">

<h3>Label flags</h3>
<table class="data fixed_table">
<tr>
  <th>Must Exist</th>
  <th>Generate on creation</th>
  <th>Generate on modification</th>
  <th>Deletable</th>
  <th>Editable</th>
  <th>Multiple</th>
  <th>Default</th>
  <th>Public</th>
</tr>
<tr>
  <td id="labelmust_exist" class="centered">{form_checkbox name=mustexist}</td>
  <td id="labelauto_on_creation" class="centered">{form_checkbox name=auto_on_creation}</td>
  <td id="labelauto_on_modification" class="centered">{form_checkbox name=auto_on_modification}</td>
  <td id="labeldeletable" class="centered">{form_checkbox name=deletable}</td>
  <td id="labeleditable" class="centered">{form_checkbox name=editable}</td>
  <td id="labelmultiple" class="centered">{form_checkbox name=multiple}</td>
  <td id="labeldefault" class="centered">{form_checkbox name=default}</td>
  <td id="labelpublic" class="centered">{form_checkbox name=public}</td>
</tr>
</table>
</div>
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}

