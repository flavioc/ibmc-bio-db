<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="{top_dir}/scripts/jquery-1.2.6.min.js"></script>
<script type="text/javascript" src="{top_dir}/scripts/custom-jquery.js"></script>
<script type="text/javascript" src="{top_dir}/scripts/functions.js"></script>
{foreach from=$scripts item=script}
<script type="text/javascript" src="{top_dir}/scripts/{$script}"></script>
{/foreach}
{foreach from=$stylesheets item=stylesheet}
<link rel="stylesheet" href="{top_dir}/styles/{$stylesheet}" type="text/css" media="screen" />
{/foreach}
<link rel="stylesheet" href="{top_dir}/styles/main.css" type="text/css" charset="utf-8" />
<title>{$title}</title>
</head>
<body>
<div id="top">
<h1><a href="{site}/welcome">Bio DB</a></h1>
</div>

<div id="leftmenu">
  {if $logged_in}
  <script>
  {literal}
  $().ready(function() {
    toggle_menu('taxonomy');
    toggle_menu('sequence');
    toggle_menu('label');
  });
  {/literal}
  </script>
  <ul>
    <li id="taxonomy_menu"><a href="#">Taxonomies</a></li>
    <ul id="taxonomy_id">
      <li><a href="{site}/taxonomy/browse">Browse</a></li>
      <li><a href="{site}/taxonomy/add">Add</a></li>
      <li><a href="{site}/rank">Ranks</a></li>
      <li><a href="{site}/taxonomy/sync">Sync</a></li>
    </ul>
    <li id="sequence_menu"><a href="#">Sequences</a></li>
    <ul id="sequence_id">
      <li><a href="{site}/sequence/browse">List</a></li>
      <li><a href="{site}/sequence/add">Add</a></li>
    </ul>
    <li id="label_menu"><a href="#">Labels</a></li>
    <ul id="label_id">
      <li><a href="{site}/label/add">Add new</a></li>
      <li><a href="{site}/label/browse">List</a></li>
    </ul>
    {if $logged_in && ($user_type == 'admin')}
      <li><a href="{site}/profile/list_all">Utilizadores</a></li>
    {/if}
  </ul>

  Logged as: <a href="{site}/profile/view/{$user_id}">{$username}</a> ({$user_type})
  {form_open to='welcome/logout' name='form_logout'}
  {form_submit name=submit msg=Logout}

  {else}

  {form_open to='welcome/login' name=login_form}

<script>
{literal}
  $(document).ready(function() {
    $("#login_form").validate({
      rules: {
        login_username: {
          required: true,
          minlength: 5,
          maxlength: 32
        },
        login_password: {
          required: true,
          minlength: 6
        }
      },
      errorPlacement: basicErrorPlacement
    });
});
{/literal}
</script>
{form_row name=login_username msg='Username:'}

{form_row name=login_password msg='Password:' type=password}

{form_submit name=login msg=Login} 

<p>{anchor to="profile/register" msg=Register}</p>

{/if}

{form_end}

</div>

<div id="content">
