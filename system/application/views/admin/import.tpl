<h2>Import database</h2>

{form_open to='admin/do_import_database' name=import_database_form multipart=yes}
<fieldset>
{form_row name=file msg="XML file:" type=upload}
</fieldset>
{form_submit name=submit msg='Import'}