{include file="header.tpl"}

<h2>View sequence</h2>

{assign var=seq_id value=$sequence.id}

<script>
{literal}
$(document).ready(function() {
  var base_site = "{/literal}{site}{literal}/sequence/";
  var seq_id = "{/literal}{$seq_id}{literal}";
  var seqdata = {seq: seq_id};

  $('#seqname').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: seqdata
  });

  $('#seqaccession').editable(base_site + 'edit_accession', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: seqdata
  });

});
{/literal}
</script>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="seqname">{$sequence.name}</span></p>
  <p><span class="desc">Type: </span><span id="seqtype">{$sequence.type}</span></p>
  <p><span class="desc">Accession Number: </span><span id="seqaccession">{$sequence.accession}</span></p>
  <p><span class="desc">Content: </span><a href="{site}/sequence/download/{$sequence.id}">Click</a></p>
</div>

{form_open name=form_delete to="sequence/delete/$seq_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

{include file="footer.tpl"}
