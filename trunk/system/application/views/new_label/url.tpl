
<h2>New URL label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_url_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide=#data_area}
<span id="data_area">
{form_row name=url msg='URL:'}
</span>
</fieldset>

{form_submit name=submit msg='Add label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  $("#form_add_label").validate({
    rules: {
      url: {
        required: generate_disabled
      }
    }
  });
});
</script>
{/literal}
