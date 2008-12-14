{include file="header.tpl"}

<h2>View label</h2>

{assign var=label_id value=$label.id}

{if !$label.default}
<script>
{literal}
$(document).ready(function() {
  var base_site = "{/literal}{site}{literal}/label/";
  var label_id = "{/literal}{$label_id}{literal}";
  var labeldata = {label: label_id};

  $('#labelname').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: labeldata
  });

  $('#labeltype').editable(base_site + 'edit_type', {
    data: {/literal}{encode_json_data data=$types key=name}{literal},
    type: "select",
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    submitdata: labeldata
  });

  $('#labelcomment').editable(base_site + 'edit_comment', {
    select: true,
    type: 'textarea',
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    cols: 45,
    rows: 5,
    submitdata: labeldata
  });

  // checkbox for autoadd

  var autoadd = $("#autoadd");
  autoadd.click(function(){
    var url = base_site + "edit_autoadd/" + label_id + "/" + (autoadd.is(":checked") ? "yes" : "no");
    $.post(url);
  });

});
{/literal}
</script>
{/if}

<div class="data_show">
  <p><span class="desc">Name: </span><span id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span id="labeltype">{$label.type}</span></p>
  <p><span class="desc">Auto Add: </span>
    <span id="labelautoadd">
      {form_open name=form_autoadd}{form_checkbox name=autoadd checked=$label.autoadd}{form_end}
    </span>
  </p>
  <p><span class="desc">Is default: </span><span id="labeldefault">{boolean value=$label.default}</span></p>
  <p><span class="desc">Comment: </span><span id="labelcomment">{if $label.comment}{$label.comment}{else}---{/if}</span></p>
</div>

{if !$label.default}


{form_open name=form_delete to="label/delete_redirect/$label_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

{/if}

{include file="footer.tpl"}
