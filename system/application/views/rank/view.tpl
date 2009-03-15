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

  <p><span class="desc">Name: </span><span id="rankname" class="writeable">{$rank.rank_name}</span></p>
  <p><span class="desc">Parent: </span><span id="rankparent" class="writeable">{if $rank.rank_parent_name}{$rank.rank_parent_name}{else}---{/if}</span></p>

{include file='history/form_view.tpl' data=$rank}

</div>

{form_open name=form_delete to="rank/delete_redirect"}
{form_hidden name=id value=$rank.rank_id}
{form_submit name=submit_delete msg=Delete}
{form_end}

{button name="list_ranks" to="rank/list_all" msg="List ranks"}
