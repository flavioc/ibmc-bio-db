<h2>Add label</h2>
<script>
{literal}
  $(document).ready(function() {
    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        },
        type: {
          required: true
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='label/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$types name=type msg='Type:' key=name}
{form_row type=checkbox name=autoadd msg='Auto add:'}
{form_row type=checkbox name=mustexist msg='Must exist:'}
{form_row type=textarea name=comment msg='Comment:' cols=50 rows=5}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}
