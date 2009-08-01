<h2>Import labels from file</h2>

{form_open to='label/do_import' name=label_batch_form multipart=yes}
<fieldset>
{form_row name=file msg="XML file:" type=upload}
</fieldset>
{form_submit name=submit msg='Import'}