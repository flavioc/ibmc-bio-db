
<h2>New integer label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_integer_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide=data_area}
<span id="data_area">
{form_row name=integer msg='Integer:'}
</span>
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  function number_required() {
    return !checkbox_enabled('#generate_check');
  }

  $("#form_add_label").validate({
    rules: {
      integer: {
        required: number_required,
        number: true
      }
    }
  });
});
</script>
{/literal}
