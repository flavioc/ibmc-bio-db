<h2>Upload sequences</h2>

{form_open to='sequence/do_add_batch' name=batch_form multipart=yes}
<fieldset>
{form_row name=file msg="FASTA/XML file:" type=upload}
{form_row name=file2 msg="Protein file:" type=upload hidden=yes}
{form_hidden name=event value=$event}
{form_row name=upload_option msg="Options" type=radio options='none,duo,generate' msgs='None,DNA/Protein,Generate Protein' checked='none'}
</fieldset>
{form_submit name=submit msg='Import'}
{form_end}

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
  
  var label = "LOADING_UPLOAD_LABEL";
  var event = $('input[name=event]').val();
  
  function update_loading()
  {
    var url = "{/literal}{site}{literal}/event/get_event_status/"+event;
    $.get(url, {}, function (data) {
      $('#loading-upload div').html(data);
    }, 'text');
  }
  
  $('#batch_form').submit(function () {
    var loading = $('#loading-upload');
    var option = $('input[name=upload_option]').fieldValue(); 
    var file1 = $('input[name=file]');
    var file2 = $('input[name=file2]');
    
    if(option == 'none') {
      $('#loading-file1').text('File: ' + file1.val()).show();
    } else if(option == 'generate') {
      $('#loading-file1').text('DNA File: ' + file1.val()).show();
    } else if(option == 'duo') {
      $('#loading-file1').text('DNA file: ' + file1.val()).show();
      $('#loading-file2').text('Protein file: ' + file2.val()).show();
    }
   
    $.blockUI({ message: $('#loading-upload'),  css: {
        color:		'#000',
        border:		'3px solid #aaa',
        backgroundColor:'#fff'
      }
    });
    
    loading.stopTime(label, update_loading).everyTime(500, label, update_loading);
   
    return true;
  });
  
});
</script>
<style>
#loading-upload {
  padding-top: 20px;
  padding-left: 10px;
  padding-right: 10px;
}
</style>
{/literal}
<div id="loading-upload" {display_none}>
{loader_pic show=yes}
<h4 id="loading-file1" {display_none}></h4>
<h4 id="loading-file2" {display_none}></h4>
<div>
</div>
</div>