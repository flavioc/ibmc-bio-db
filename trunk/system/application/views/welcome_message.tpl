{if $logged_in}
<h2>Welcome {$username}</h2>
{else}
<h1>Welcome</h1>
{/if}

{if $logged_in}
<p class="image-para">
  <a href="{site}/sequence/search"><img src="{top_dir}/images/site1.png"/></a>
  <a href="{site}/sequence/add_batch"><img src="{top_dir}/images/site2.png" /></a>
</p>
<p class="image-para">
  <a href="{site}/taxonomy/browse"><img src="{top_dir}/images/site3.png" /></a>
  <a href="{site}/sequence/browse"><img src="{top_dir}/images/site4.png" /></a>
</p>
{else}
<p class="image-para">
  <a href="{site}/sequence/search"><img src="{top_dir}/images/site1.png"/></a>
  <img src="{top_dir}/images/site2.png" />
</p>
<p class="image-para">
  <a href="{site}/taxonomy/browse"><img src="{top_dir}/images/site3.png" /></a>
  <a href="{site}/sequence/browse"><img src="{top_dir}/images/site4.png" /></a>
</p>
{/if}