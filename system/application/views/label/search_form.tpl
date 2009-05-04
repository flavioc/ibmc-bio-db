<p>
{form_open name=form_label_search}
{form_row name=label_name msg='Name:'}
{form_row type=select data=$types name=label_type msg='Type:' key=name blank=yes}
{form_row type=select data=$users name=label_user msg='User:' key=id blank=yes}
{form_submit name=submit_search msg=Filter}
{form_end}
</p>

