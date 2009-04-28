<h2>User {$user.name}</h2>

<p><img src="{site}/image/get_id/{$user.id}/50" /></p>
<ul>
  <li>Name: {$user.name}</li>
  <li>Complete name: {$user.complete_name}</li>
  <li>Email: {$user.email}</li>
  {if $user.birthday}
    <li>Birthday: {$user.birthday}</li>
  {/if}
  <li>User type: {if $user.user_type == 'admin'}Administrador{else}Normal{/if}</li>
</ul>
{if $user.id == $user_id}
<p><a href="{site}/profile/edit">Edit profile</a></p>
{else}
{if $is_admin}
{form_open name=form_delete to="profile/delete_redirect"}
{form_hidden name=id value=$user.id}
{form_submit name=delete_button msg=Delete}
{form_end}

<script>
{to_js var=user value=$user}
{literal}
$(document).ready(function () {
  activate_delete_dialog(get_app_url() + '/profile/delete_dialog/' + user.id);
});
{/literal}
</script>
{/if}
{/if}

