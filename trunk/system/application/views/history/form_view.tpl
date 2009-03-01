<p><span class="desc">Last modification: </span>{if $data.update}{$data.update}{else}---{/if}</span></p>
<p><span class="desc">Last user: </span>{if $data.update_user_id}<a href="{site}/profile/view/{$data.update_user_id}">{$data.user_name}</a>{else}---{/if}</span></p>
