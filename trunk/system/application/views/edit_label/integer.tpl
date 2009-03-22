
<h2>Edit integer label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_integer_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=integer msg='Integer:' value=$label.int_data}
</span>
</fieldset>

{form_submit name=submit msg='Edit label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_edit_label").validate({
    rules: {
      integer: {
        required: generate_disabled,
        number: true
      }
    }
  });
});
</script>
{/literal}
