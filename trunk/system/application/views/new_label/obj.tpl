
<h2>New object label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_obj_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=file msg="File:" type=upload}
</span>
</fieldset>

{form_submit name=submit_file msg='Add label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_add_label").validate({
    rules: {
      file: {
        required: generate_disabled
      }
    }
  });
});
</script>
{/literal}
