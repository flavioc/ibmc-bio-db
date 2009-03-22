
<h2>Edit text label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_text_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=text msg='Text:' value=$label.text_data}
</span>
</fieldset>

{form_submit name=submit msg='Edit label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_edit_label").validate({
    rules: {
      text: {
        required: generate_disabled
      }
    }
  });
});
</script>
{/literal}
