{form_open name=form_search}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$ranks name=rank msg='Rank:' blank=yes start=0 key=rank_id value=rank_name}
{form_row type=select data=$trees name=tree msg='Tree:' blank=yes start=0}
</fieldset>
{form_submit name=submit_search msg=Search}
{form_end}