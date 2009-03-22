
<h2>Edit object label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_obj_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=file msg="File:" type=upload}
</span>
</fieldset>

{form_submit name=submit_file msg='Edit label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_edit_label").validate({
    rules: {
      file: {
        required: generate_disabled
      }
    }
  });
});
</script>
{/literal}
