<h2>Edit settings</h2>

<script>
{literal}
  $(document).ready(function() {
    $("#edit_form").validate({
      rules: {
        paging_size: {
          number: true,
          required: true,
          min: 1,
          max: 1000
        },
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='profile/edit_settings' name=edit_form}
<fieldset>
{form_row name=paging_size msg='Paging size:' size=10}
</fieldset>
{form_submit name=submit msg='Save'}
{form_end}
