<h2>Add tree</h2>
<script>
{literal}
  $(document).ready(function() {
    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 128
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='tree/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}
