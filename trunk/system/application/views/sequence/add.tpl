<h2>Add sequence</h2>
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
        content: {
          maxlength: 65535,
          required: true
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='sequence/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=textarea name=content msg='Content:' cols=50 rows=15}
{form_row type=checkbox name=protein msg="Generate protein:"}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}
