
<h2>Edit position label</h2>

{include file=edit_label/info.tpl}

{form_open name=form_edit_label to="label_sequence/edit_position_label"}

<fieldset>
{include file=edit_label/hidden.tpl}
{include file=edit_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=start msg="Start:" value=$label.position_a_data}
{form_row name=length msg="Length:" value=$label.position_b_data}
</span>
</fieldset>

{form_submit name=submit_pos msg='Edit label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_edit_label").validate({
    rules: {
      start: {
        required: generate_disabled
      },
      length: {
        required: generate_disabled
      }
    }
  });
});
</script>
{/literal}
