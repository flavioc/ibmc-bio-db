{if $with_username}
  {form_row name=username msg='Username:'}
{/if}
{form_row name=complete_name msg='Complete name:' size=50}
{form_row name=email msg='Email:'}

{form_row name=password1 msg='Password:' type=password autocomplete='off'}
{form_row name=password2 msg='Retype password:' type=password autocomplete='off'}
