<h2>Add label</h2>
<script>
{literal}
  $(document).ready(function() {
    var auto_on_creation = $("#auto_on_creation");
    var auto_on_modification = $("#auto_on_modification");

    function code_is_required() {
      var checked_creation = auto_on_creation.is(":checked");
      var checked_modification = auto_on_modification.is(":checked");

      return checked_creation || checked_modification;
    }

    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        },
        type: {
          required: true
        },
        code: {
          required: code_is_required
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
{form_row type=checkbox name=auto_on_creation msg='Generate on creation:'}
{form_row type=checkbox name=auto_on_modification msg='Generate on modification:'}<br />
{form_row type=textarea name=code msg='Code:' cols=50 rows=5}
{form_row type=textarea name=comment msg='Comment:' cols=50 rows=5}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}

