{include file="header.tpl"}
{if $user}
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
{/if}
{else}
<p>Invalid user id</p>
{/if}

{include file="footer.tpl"}
