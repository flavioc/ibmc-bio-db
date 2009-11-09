{include file=common_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{if $files && count($files) > 0}
{form_row msg='Stored file:' type='select' blank=yes name=stored_file start=0 data=$files key=id}
{literal}<script>
  $(function () {
    $('#stored_file').change(function () {
      var selected = $("#stored_file option:selected");
      
      if(selected.val() == 0)
        $('#file_label_row').show();
      else
        $('#file_label_row').hide();
    });
  });
</script>{/literal}
{/if}
<div id="file_label_row">
  {form_row name=file msg="File:" type=upload}
</div>
{include file=common_label/param.tpl}
</span>