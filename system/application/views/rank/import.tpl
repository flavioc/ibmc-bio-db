<h2>Import ranks from file</h2>

{form_open to='rank/do_import' name=rank_batch_form multipart=yes}
<fieldset>
{form_row name=file msg="XML file:" type=upload}
</fieldset>
{form_submit name=submit msg='Import'}