<h2>Upload sequence file</h2>

{form_open to='sequence/do_add_batch' name=batch_form multipart=yes}
<fieldset>
{form_row name=file msg="FASTA/XML file:" type=upload}
</fieldset>
{form_submit name=submit msg='Import'}

