<h2>Upload sequences</h2>

{form_open to='sequence/do_add_batch' name=batch_form multipart=yes}
<fieldset>
{form_row name=file msg="FASTA/XML file:" type=upload}
{form_row name=file2 msg="Protein file:" type=upload hidden=yes}

{form_row name=upload_option msg="Options" type=radio options='none,duo,generate' msgs='None,DNA/Protein,Generate Protein' checked='none'}
</fieldset>
{form_submit name=submit msg='Import'}

{literal}<script>
$(function () {
  var second_file = $('#file2').parent();
  
  {/literal}{if !$file2_error}second_file.hide();{/if}{literal}
  
  $('input[name=upload_option][value=none]').attr('checked', 'checked');
  
  $('input[name=upload_option]').change(function () {
    var $this = $(this);
    var first_label = $('#file').prev();
    var val = $this.val();
    
    if(val == 'duo' || val == 'generate') {
      first_label.text('DNA file:');
    } else {
      first_label.text('FASTA/XML file:');
    }
    
    if(val == 'duo') {
      second_file.show();
    } else {
      second_file.hide();
    }
    
    return true;
  });
});
</script>{/literal}