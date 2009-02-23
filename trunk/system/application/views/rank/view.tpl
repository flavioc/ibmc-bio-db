<h2>View/Edit rank</h2>

<script>
{literal}
$(document).ready(function() {
  var base_site = "{/literal}{site}{literal}/rank/";
  var rank_id = "{/literal}{$rank.rank_id}{literal}";
  var rankdata = {rank: rank_id};

  $('#rankname').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    submitdata: rankdata
  });

  $('#rankparent').editable(base_site + 'edit_parent', {
    data: {/literal}{encode_json_data data=$ranks blank=yes}{literal},
    type: "select",
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    submitdata: rankdata
  });
});
{/literal}
</script>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="rankname">{$rank.rank_name}</span></p>
  <p><span class="desc">Parent: </span><span id="rankparent">{if $rank.rank_parent_name}{$rank.rank_parent_name}{else}---{/if}</span></p>
</div>

{assign var=rank_id value=$rank.rank_id}

{form_open name=form_delete to="rank/delete_redirect/$rank_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

<p>
<a href="{site}/rank/list_all">List Ranks</a>
</p>
