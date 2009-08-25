<h2>View/Edit rank</h2>

{if $logged_in}
<script>
{to_js var=rank value=$rank}
{literal}
$(function() {
  var base_site = get_app_url() + "/rank/";
  var rank_id = rank.rank_id;
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
{/if}

<div class="data_show">

  <p><span class="desc">Name: </span><span id="rankname">{$rank.rank_name}</span></p>
  <p><span class="desc">Parent: </span><span id="rankparent">{if $rank.rank_parent_name}{$rank.rank_parent_name}{else}---{/if}</span></p>

{include file='history/form_view.tpl' data=$rank}

</div>

{if $logged_in}
{form_open name=form_delete to="rank/delete_redirect"}
{form_hidden name=id value=$rank.rank_id}
{form_submit name=delete_button msg=Delete}
{form_end}
{/if}

{button name="list_ranks" to="rank/list_all" msg="List ranks"}

{if $logged_in}
<script>{literal}
$(function () {
  activate_delete_dialog(get_app_url() + '/rank/delete_dialog/' + rank.rank_id);
  $('#rankname, #rankparent').addClass("writeable");
});
{/literal}</script>
{/if}

{literal}<style>
#form_delete, #form_list_ranks {
  display: inline;
}
</style>{/literal}
