<h2>View/Edit sequence</h2>

{assign var=seq_id value=$sequence.id}

<script>
{literal}
$(document).ready(function() {
  $('#seqname').editable(base_site + '/edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    submitdata: seqdata
  });

  $('#seqaccession').editable(base_site + '/edit_accession', {
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
    rows: 5,
    submitdata: seqdata,
    finishHook: reload_labels_list
  });

  $('#labels_list').gridEnable({paginate: false});
  $('#missing_list').gridEnable({paginate: false});
  $('#addable_list').gridEnable({paginate: false});

  hide_addable_list();
  hide_missing_list();

  $('#hide_show_labels').minusPlus({
    enabled: true,
    plusEnabled: load_labels_list,
    minusEnabled: hide_labels_list
  });

  $('#hide_show_missing').minusPlus({
    enabled: false,
    plusEnabled: load_missing_list,
    minusEnabled: hide_missing_list
  });

  $('#hide_show_missing_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#missing_list').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#missing_list').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });

  $('#hide_show_addable_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#addable_list').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#addable_list').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });

  $('#hide_show_addable').minusPlus({
    plusEnabled: load_addable_list,
    minusEnabled: hide_addable_list
  });

});
{/literal}
</script>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="seqname">{$sequence.name}</span></p>
  <p><span class="desc">Type: </span><span id="seqtype">{$sequence.type}</span></p>
  <p><span class="desc">Accession Number: </span><span id="seqaccession">{$sequence.accession}</span></p>
  <p><span class="desc">Content: </span><span id="seqcontent">{$sequence.content}</span></p>

{include file='history/form_view.tpl' data=$sequence}

</div>

{form_open name=form_delete to="sequence/delete/$seq_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

<p>
<h3>Associated labels</h3>
<div id="hide_show_labels"></div>
<div id="labels_list">
</div>
</p>

<hr />

<p>
<h3>Missing labels</h3><div id="hide_show_missing"></div>
<div id="missing_box">
  <div id="missing_list">
  </div>
  <br />
  <div id="hide_show_missing_details">
  </div>
</div>
</p>

<hr />

<p>
<h3>Addable labels</h3>
<div id="hide_show_addable"></div>
<div id="addable_box">
  <div id="addable_list">
  </div>
  <br />
  <div id="hide_show_addable_details">
  </div>
</div>
</p>

<hr />

<p>
<br />
<a href="{site}/sequence/browse">Sequence List</a>
</p>

