<h2>View/Edit tree</h2>

<script>
{to_js var=tree value=$tree}
{literal}
$(document).ready(function() {
  var base_site = get_app_url() + "/tree/";
  var tree_id = tree.id;
  var treedata = {tree: tree_id};

  $('#treename').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    submitdata: treedata
  });
});
{/literal}
</script>

<div class="data_show">

  <p><span class="desc">Name: </span><span id="treename" class="writeable">{$tree.name}</span></p>

{include file='history/form_view.tpl' data=$tree}

</div>

{form_open name=form_delete to="tree/delete_redirect"}
{form_hidden name=id value=$tree.id}
{form_submit name=delete_button msg=Delete}
{form_end}

{form_open name=form_export to="tree/export"}
{form_hidden name=id value=$tree.id}
{form_submit name=export_button msg=Export}
{form_end}

{form_open name=form_browse to="taxonomy/tree_browse" method=get}
{form_hidden name=start value=$tree.id}
{form_submit name=browse_button msg=Browse}
{form_end}

{button name="list_tree" to="tree" msg="List trees"}

{literal}<script>
$(function () {
  activate_delete_dialog(get_app_url() + '/tree/delete_dialog/' + tree.id);
});
</script>
<style>
#form_delete, #form_export, #form_list_tree, #form_browse {
  display: inline;
}
</style>{/literal}
