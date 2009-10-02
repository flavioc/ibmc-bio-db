{form_open name=form_search}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$users name=user msg='User:' key=id blank=yes start=0}
</fieldset>
{form_submit name=submit_search msg=Filter}
{form_end}