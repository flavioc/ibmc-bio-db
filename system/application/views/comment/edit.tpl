<h2>Edit database description</h2>

{form_open to='comment/do_edit' name=edit_form}
<fieldset>
{form_row type=textarea name=comment msg='Description:' cols=50 rows=10 value=$comment}
</fieldset>
{form_submit name=submit msg='Edit'}
{form_end}
