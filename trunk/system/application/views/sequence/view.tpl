{if $logged_in}
<h2>View/Edit sequence</h2>
{else}
<h2>View sequence</h2>
{/if}

{if $logged_in}
<script>
{to_js var=sequence value=$sequence}

{literal}
$(document).ready(function() {
  seq_id = sequence.id;
  var seqdata = {seq: seq_id};

  $('#seqname').editable(base_site + '/edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    submitdata: seqdata
  });

  $('#seqcontent').editable(base_site + '/edit_content', {
    select: true,
    type: 'textarea',
    submit: 'OK',
    cancel: 'cancel',
    style: 'inherit',
    cols: 50,
    rows: 15,
    submitdata: seqdata,
    finishHook: reload_labels_list,
    loadurl: base_site + '/fetch/' + sequence.id
  });
  
  $('#seqname, #seqcontent').addClass('writeable');
});
{/literal}
</script>
{/if}

<div class="data_show">
  <p><span class="desc">Name: </span><span id="seqname">{$sequence.name}</span></p>
  <p><span class="desc"><a href="{site}/sequence/download/{$sequence.id}">Content</a>: </span><span id="seqcontent">{$sequence.content}...</span></p>
  {if $trans_sequence}
  <p><span class="desc">Translated:</span><a href="{site}/sequence/view/{$trans_sequence.id}">{$trans_sequence.name}</a></p>
  {/if}
{include file='history/form_view.tpl' data=$sequence}

</div>

{form_open to='sequence/export' name=export_form}
{form_hidden name=id value=$sequence.id}
{include file=sequence/export_types.tpl}
{form_submit name=submit msg='Export'}
{form_end}

{button name="labels_btn" msg="View Labels" to="sequence/labels/$id"}

{if $logged_in}
{form_open name=form_delete to="sequence/delete_redirect"}
{form_hidden name=id value=$sequence.id}
{form_submit name=delete_button msg=Delete}
{form_end}

<script>{literal}
$(document).ready(function () {
  activate_delete_dialog(get_app_url() + '/sequence/delete_dialog/' + sequence.id);
});
</script>
{/literal}
{/if}

{literal}<style>
#export_form, #form_labels_btn, #form_delete {
  display: inline;
}
</style>{/literal}