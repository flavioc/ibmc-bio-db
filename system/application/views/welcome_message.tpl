{include file="header.tpl"}

{if $logged_in}

<h2>Welcome {$username}</h2>

{else}
<h1>Welcome</h1>

<p>Please login</p>

{/if}

{include file="footer.tpl"}
