{include file="header.tpl"}
<h2>Register user</h2>

{form_open to='profile/do_register' name=register_form multipart=yes}
<fieldset>
<script>
{literal}
  $(document).ready(function() {
    $("#register_form").validate({
      rules: {
        username: {
          required: true,
          minlength: 5,
          maxlength: 32
        },
        complete_name: {
          maxlength: 512
        },
        email: {
          required: true,
          email: true
        },
        password1: {
          required: true,
          minlength: 6
        },
        password2: {
          required: true,
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

{include file="user_form.tpl" with_username=true}
</fieldset>
{form_submit name=submit msg='Do register'}
{form_end}

{include file="footer.tpl"}
