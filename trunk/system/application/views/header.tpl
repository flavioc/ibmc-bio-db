<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
{include_js name=jquery}
{include_js name=functions}
{include_js name=custom-jquery}
{include_js name=constants}
{foreach from=$scripts item=script}
  {include_js name=$script}
{/foreach}
{foreach from=$stylesheets item=stylesheet}
<link rel="stylesheet" href="{top_dir}/styles/{$stylesheet}?random={random}" type="text/css" media="screen" />
{/foreach}
<link rel="stylesheet" href="{top_dir}/styles/main.css?random={random}" type="text/css" charset="utf-8" />
<link rel="shortcut icon" href="{top_dir}/images/favicon.ico" />
<title>{$title}</title>
</head>
<body>
<div id="top">
<div id="left_top">
<img src="{top_dir}/images/dna.jpg" alt="BIO DB"/>
<h1><a href="{site}/welcome">Bio DB</a></h1>
<span id="comment_db">{$comment_header}</span>
</div>
<div id="right_top">
<form id="form_search_global">
<input type="text" name="firstname" />
<input type="submit" value="Search"/>
</form>
<a id="number_sequences" href="{site}/sequence/browse">{$total_seqs} saved sequences</a>
</div>
</div>

<div id="leftmenu">
  {if $logged_in}
{literal}
<script>
  $().ready(function() {
    toggle_menu('taxonomy');
    toggle_menu('sequence');
    toggle_menu('label');
    toggle_menu('user');
    toggle_menu('rank');
    toggle_menu('tree');
    toggle_menu('admin');
  });
</script>
{/literal}
  <ul>
    <li id="taxonomy_menu"><a href="#">Taxonomies</a></li>
    <ul id="taxonomy_id">
      <li><a href="{site}/taxonomy/browse">Browse</a></li>
      <li><a href="{site}/taxonomy/tree_browse">Tree Browse</a></li>
      <li><a href="{site}/taxonomy/add">Add</a></li>
      <li><a href="{site}/taxonomy/sync">Sync</a></li>
    </ul>
    <li id="tree_menu"><a href="#">Trees</a></li>
    <ul id="tree_id">
      <li><a href="{site}/tree/add">Add</a></li>
      <li><a href="{site}/tree">List</a></li>
    </ul>
    <li id="rank_menu"><a href="#">Ranks</a></li>
    <ul id="rank_id">
      <li><a href="{site}/rank/add">Add</a></li>
      <li><a href="{site}/rank/list_all">List</a></li>
      <li><a href="{site}/rank/export">Export</a></li>
      <li><a href="{site}/rank/import">Import</a></li>
    </ul>
    <li id="sequence_menu"><a href="#">Sequences</a></li>
    <ul id="sequence_id">
      <li><a href="{site}/sequence/add">Add</a></li>
      <li><a href="{site}/sequence/add_batch">Batch</a></li>
      <li><a href="{site}/sequence/browse">List</a></li>
      <li><a href="{site}/sequence/search">Search</a></li>
      <li><a href="{site}/sequence/search?type=dna">DNA Search</a></li>
      <li><a href="{site}/sequence/search?type=protein">Protein Search</a></li>
    </ul>
    <li id="label_menu"><a href="#">Labels</a></li>
    <ul id="label_id">
      <li><a href="{site}/label/add">Add</a></li>
      <li><a href="{site}/label/browse">List</a></li>
      <li><a href="{site}/label/export">Export</<a></li>
      <li><a href="{site}/label/import">Import</a></li>
    </ul>
    <li id="user_menu"><a href="#">Users</a></li>
    <ul id="user_id">
      <li><a href="{site}/profile/edit">Edit profile</a></li>
      <li><a href="{site}/profile/settings">Settings</a></li>
    </ul>
    <li><a href="{site}/comment/edit">Comment</a></li>
    {if $logged_in && ($user_type == 'admin')}
    <li id="admin_menu"><a href="#">Administration</a></li>
    <ul id="admin_id">
      <li><a href="{site}/admin/drop_database">Reset database</a></li>
      <li><a href="{site}/profile/list_all">List users</a></li>
      <li><a href="{site}/profile/register">Register user</a></li>
    </ul>
    {/if}
  </ul>

  Logged as: <a href="{site}/profile/view/{$user_id}">{$username}</a> ({$user_type})
  {form_open to='welcome/logout' name='form_logout'}
  {form_submit name=submit msg=Logout}

  {else}

{form_open to='welcome/login' name=login_form}

{if $redirect}
{form_hidden name=redirect value=$redirect}
{/if}

<script>
{literal}
  $(document).ready(function() {
    $("#login_form").validate({
      rules: {
        login_username: {
          required: true,
          minlength: 2,
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

<!-- <p>{anchor to="profile/register" msg=Register}</p>-->

{/if}

{form_end}

</div>

<div id="content">

{if $error_msg}
<script>
{literal}
  $().ready(function () {
    add_new_error_message("{/literal}{$error_msg}{literal}");
  });
{/literal}
</script>
{/if}

