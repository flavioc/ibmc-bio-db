<h2>Change database background</h2>

{form_open to='admin/do_change_background' name=form multipart=yes}
<fieldset>
{form_row name=file msg="Image:" type=upload}
</fieldset>
{if $has_background}
{form_submit name=submit msg='Remove current'}
{/if}
{form_submit name=submit msg='Save'}
{form_end}