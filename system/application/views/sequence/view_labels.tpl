{include file=loading.tpl}

<h2>Sequence Labels</h2>

<div class="data_show">
  <p><span class="desc">Name: </span><a href="{site}/sequence/view/{$sequence.id}">{$sequence.name}</a></p>
  <p><span class="desc"><a href="{site}/sequence/download/{$sequence.id}">Content</a>: </span>{$sequence.content}...</p>
  {if $trans_sequence}
  <p><span class="desc">Translated:</span><a href="{site}/sequence/labels/{$trans_sequence.id}">{$trans_sequence.name}</a></p>
  {/if}
  {if $super}
  <p><span class="desc">Super:</span><a href="{site}/sequence/view/{$super.id}">{$super.name}</a></p>
  {/if}
  {if $lifetime}
  <p><span class="desc">Lifetime:</span>{$lifetime}</p>
  {/if}
</div>

<script>
{to_js var=sequence value=$sequence}
var seq_id = sequence.id;
{literal}
$(function() {
  $('#labels_list').gridEnable({paginate: false});

  $('#form_add_label').livequery(function () {
    $('#label_error').hide();
    $(this).ajaxFormAdd();
  });
  
  $('#form_edit_label').livequery(function () {
    $('#label_error').hide();
    $(this).ajaxFormEdit();
  });
  
  $('#date').livequery(function () {
    $(this).datePickerDate();
  });
  
  $('#hide_show_labels').minusPlus({
    enabled: true,
    plusEnabled: load_labels_list,
    minusEnabled: hide_labels_list
  });

  $('#hide_show_labels_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#labels_list').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#labels_list').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });
});
{/literal}
</script>

<p>
<h3>Associated labels</h3>
<div id="hide_show_labels"></div>
<div id="labels_box">
  <div id="labels_list">
  </div>
  <br />
  <div id="hide_show_labels_details">
  </div>
</div>
</p>

{if $missing}
<script>
{literal}
$(document).ready(function () {

  $('#missing_list').gridEnable({paginate: false});

  hide_missing_list();

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
});

{/literal}
</script>

<hr />

<p>
<h3>Missing labels</h3>
<div id="hide_show_missing"></div>
<div id="missing_box">
  <div id="missing_list">
  </div>
  <br />
  <div id="hide_show_missing_details">
  </div>
</div>
</p>

{/if}

{if $logged_in}

<script>
{literal}
$(document).ready(function () {
  $('#addable_list').gridEnable({paginate: false});

  hide_addable_list();

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

  {/literal}{if $missing}
  ensure_addable_list_loaded();
  {/if}{literal}
});
{/literal}
</script>

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
{/if}

<hr />

{if $bad_multiple}
<script>
{literal}
$(document).ready(function () {

  $('#bad_multiple_list').gridEnable({paginate: false});

  hide_bad_multiple_list();

  $('#hide_show_bad_multiple').minusPlus({
    enabled: false,
    plusEnabled: load_bad_multiple_list,
    minusEnabled: hide_bad_multiple_list
  });
});

{/literal}
</script>

<hr />

<p>
<h3>Bad multiple</h3>
<div id="hide_show_bad_multiple"></div>
<div id="bad_multiple_box">
  <div id="bad_multiple_list">
  </div>
  <br />
</div>
</p>
{/if}
