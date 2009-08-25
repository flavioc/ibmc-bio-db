<h2>Edit user</h2>

<script>
{literal}
  $(document).ready(function() {
    $("#edit_form").validate({
      rules: {
        old_password: {
          required: true,
          minlength: 6
        },
        complete_name: {
          maxlength: 512
        },
        email: {
          required: true,
          email: true
        },
        password1: {
          required: false,
          minlength: 6
        },
        password2: {
          required: false,
          minlength: 6,
          equalTo: "#password1"
        },
        image: {
          accept: "bmp|png|jpg|gif"
        }
      },
      messages: {
        image: {
          accept: "Please enter a valid image file."
        }
      },
      errorPlacement: birthdayErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='profile/do_edit' name=edit_form multipart=yes}
<fieldset>
{form_hidden name=id value=$user.id}
{if $is_admin && $user_id != $user.id}
{form_row name=old_password msg='Your password:' type=password autocomplete='off'}
{else}
{form_row name=old_password msg='Current password:' type=password autocomplete='off'}
{/if}

{include file=profile/form.tpl}
</fieldset>
{form_submit name=submit msg='Do edit'}
{form_end}
