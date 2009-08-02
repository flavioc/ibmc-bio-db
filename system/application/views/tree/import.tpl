<h2>Import tree from file</h2>

{form_open to='tree/do_import' name=tree_batch_form multipart=yes}
<fieldset>
{form_row name=file msg="XML file:" type=upload}
</fieldset>
{form_submit name=submit msg='Import'}