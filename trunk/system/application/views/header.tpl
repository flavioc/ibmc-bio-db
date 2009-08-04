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
<a href="{site}/welcome"><img src="{top_dir}/images/dna.jpg" alt="BIO DB"/></a>
<h1><a href="{site}/welcome">Bio DB</a></h1>
<span id="comment_db">{$comment_header}</span>
</div>
<div id="right_top">
<form id="form_search_global" method="get" action="{site}/wide_search/search">
{if $search_term_input}
<input type="text" name="search" value="{$search_term_input}" />
{else}
<input type="text" name="search" autocomplete=off/>
{/if}
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
    $('#form_search_global input[type=text]').textGrow({pad: 25, max_limit: 700, min_limit: 300});
  });
</script>
{/literal}
  <ul id="main_menu">
    <li id="search_menu"><a href="#">Search</a>
    <ul id="search_id">
      <li><a href="{site}/sequence/search?type=dna">DNA</a></li>
      <li><a href="{site}/sequence/search?type=protein">Protein</a></li>
      <li><a href="{site}/sequence/search">All</a></li>
    </ul></li>
    <li id="sequence_menu"><a href="#">Sequences</a>
    <ul id="sequence_id">
      <li><a href="{site}/sequence/add">Add</a></li>
      <li><a href="{site}/sequence/add_batch">Batch</a></li>
      <li><a href="{site}/sequence/browse">List</a></li>
    </ul></li>
    <li id="label_menu"><a href="#">Labels</a>
    <ul id="label_id">
      <li><a href="{site}/label/add">Add</a></li>
      <li><a href="{site}/label/browse">List</a></li>
      <li><a href="{site}/label/export">Export</a></li>
      <li><a href="{site}/label/import">Import</a></li>
    </ul></li>
    <li id="taxonomy_menu"><a href="#">Taxonomies</a>
    <ul id="taxonomy_id">
      <li><a href="{site}/taxonomy/browse">Browse</a></li>
      <li><a href="{site}/taxonomy/tree_browse">Tree Browse</a></li>
      <li><a href="{site}/taxonomy/add">Add</a></li>
      <!-- <li><a href="{site}/taxonomy/sync">Sync</a></li> -->
    </ul></li>
    <li id="tree_menu"><a href="#">Trees</a>
    <ul id="tree_id">
      <li><a href="{site}/tree/add">Add</a></li>
      <li><a href="{site}/tree">List</a></li>
      <li><a href="{site}/tree/import">Import</a></li>
    </ul></li>
    <li id="rank_menu"><a href="#">Ranks</a>
    <ul id="rank_id">
      <li><a href="{site}/rank/add">Add</a></li>
      <li><a href="{site}/rank/list_all">List</a></li>
      <li><a href="{site}/rank/export">Export</a></li>
      <li><a href="{site}/rank/import">Import</a></li>
    </ul></li>
    <li id="user_menu"><a href="#">Users</a>
    <ul id="user_id">
      <li><a href="{site}/profile/edit/{$user_id}">Edit profile</a></li>
      <li><a href="{site}/profile/settings">Settings</a></li>
    </ul></li>
    <li><a href="{site}/comment/edit">Comment</a></li>
    {if $logged_in && ($user_type == 'admin')}
    <li id="admin_menu"><a href="#">Administration</a>
    <ul id="admin_id">
      <li><a href="{site}/admin/drop_database">Reset database</a></li>
      <li><a href="{site}/profile/list_all">List users</a></li>
      <li><a href="{site}/profile/register">Register user</a></li>
    </ul></li>
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
  $(function() {
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

