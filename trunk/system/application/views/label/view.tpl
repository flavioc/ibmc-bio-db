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
    cols: 50,
    rows: 5,
    submitdata: labeldata
  });

  $('#labelcode').editable(base_site + 'edit_code', {
    select: true,
    type: 'textarea',
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    cols: 50,
    rows: 5,
    submitdata: labeldata
  });

  function enable_edit_checkbox(dom, what)
  {
    var obj = $(dom);

    obj.click(function() {
      var url = base_site + "edit_" + what + "/" + label_id + "/" +
            (obj.is(":checked") ? "yes" : "no");
      $.post(url);
    });
  }

  // checkbox for autoadd
  enable_edit_checkbox("#autoadd", "autoadd");
  enable_edit_checkbox("#mustexist", "mustexist");
  enable_edit_checkbox("#auto_on_creation", "auto_on_creation");
  enable_edit_checkbox("#auto_on_modification", "auto_on_modification");

});
{/literal}
</script>
{/if}

<div class="data_show">
  <p><span class="desc">Name: </span><span id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span id="labeltype">{$label.type}</span></p>

  <p><span class="desc">Auto Add: </span>
    <span id="labelautoadd">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {form_open name=form_autoadd}{form_checkbox name=autoadd checked=$label.autoadd}{form_end}
    {/if}
    </span>
  </p>

  <p><span class="desc">Must Exist: </span>
    <span id="labelmustexist">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {form_open name=form_mustexist}
        {form_checkbox name=mustexist checked=$label.must_exist}
      {form_end}
    {/if}
    </span>
  </p>

  <p><span class="desc">Generate on creation: </span>
    <span id="labelauto_on_creation">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {form_open name=form_auto_on_creation}
        {form_checkbox name=auto_on_creation checked=$label.auto_on_creation}
      {form_end}
    {/if}
    </span>
  </p>

  <p><span class="desc">Generate on modification: </span>
    <span id="labelauto_on_modification">
    {if $label.default}
      {boolean value=$label.default}
    {else}
      {form_open name=form_auto_on_modification}
        {form_checkbox name=auto_on_modification checked=$label.auto_on_modification}
      {form_end}
    {/if}
    </span>
  </p>
  <br />

  <p><span class="desc">Is default: </span><span id="labeldefault">{boolean value=$label.default}</span></p>

  <p><span class="desc">Code: </span>
    <span class="code" id="labelcode">{if $label.code}{$label.code}{else}---{/if}</span>
  </p>

  <p><span class="desc">Comment: </span>
     <span class="comment" id="labelcomment">{if $label.comment}{$label.comment}{else}---{/if}</span>
  </p>
</div>

{if !$label.default}

{form_open name=form_delete to="label/delete_redirect/$label_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

{/if}
