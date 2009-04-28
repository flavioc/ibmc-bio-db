<h2>View label</h2>

<script>
{to_js var=rank value=$rank}
{literal}
$(document).ready(function () {
  var base_site = get_app_url() + "/label/";
  var label_id = label.id;
  var senddata = {label: label_id};

  $('#labelname').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    submitdata: senddata
  });

  $('#labeltype').editable(base_site + 'edit_type', {
    data: {/literal}{encode_json_data data=$types key=name}{literal},
    type: "select",
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    submitdata: senddata
  });

  $('#labelcode').editable(base_site + 'edit_code', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    type: 'textarea',
    cols: 70,
    rows: 15,
    submitdata: senddata
  });

  $('#labelvalidcode').editable(base_site + 'edit_validcode', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    type: 'textarea',
    cols: 70,
    rows: 15,
    submitdata: senddata
  });

  $('#labelcomment').editable(base_site + 'edit_comment', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: '150px',
    style: 'inherit',
    type: 'textarea',
    cols: 70,
    rows: 15,
    submitdata: senddata
  });

  function enable_edition_bool(what) {
    $('#label' + what).editable(base_site + 'edit_bool/' + what, {
      data: "{\"1\": \"Yes\", \"0\": \"No\"}",
      type: "select",
      submit: "OK",
      cancel: "cancel",
      style: "inherit",
      submitdata: senddata
    });
  }

  enable_edition_bool('must_exist');
  enable_edition_bool('auto_on_creation');
  enable_edition_bool('auto_on_modification');
  enable_edition_bool('deletable');
  enable_edition_bool('editable');
  enable_edition_bool('multiple');
  enable_edition_bool('default');
  enable_edition_bool('public');
});
</script>
{/literal}

<div class="data_show">
  <p><span class="desc">Name: </span><span class="writeable" id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span class="writeable" id="labeltype">{$label.type}</span></p>
  <p><span class="desc">Code: </span>
    <span class="code writeable" id="labelcode">{if $label.code}{$label.code}{else}---{/if}</span>
  </p>
  <p><span class="desc">Validation code: </span>
    <span class="code writeable" id="labelvalidcode">{if $label.valid_code}{$label.valid_code}{else}---{/if}</span>
  </p>
  <p><span class="desc">Comment: </span>
     <span class="comment writeable" id="labelcomment">{if $label.comment}{$label.comment}{else}---{/if}</span>
  </p>

{include file='history/form_view.tpl' data=$label}

<div class="grid_form">
<h3>Label flags</h3>
<table class="data" style="table-layout: fixed;">
<tr>
  <th>Must Exist</th>
  <th>Generate on creation</th>
  <th>Generate on modification</th>
  <th>Deletable</th>
  <th>Editable</th>
  <th>Multiple</th>
  <th>Default</th>
  <th>Public</th>
</tr>
<tr>
  <td class="centered"><span id="labelmust_exist" class="writeable">{boolean value=$label.must_exist}</span></td>
  <td class="centered"><span id="labelauto_on_creation" class="writeable">{boolean value=$label.auto_on_creation}</span></td>
  <td class="centered"><span id="labelauto_on_modification" class="writeable">{boolean value=$label.auto_on_modification}</span></td>
  <td class="centered"><span id="labeldeletable" class="writeable">{boolean value=$label.deletable}</span></td>
  <td class="centered"><span id="labeleditable" class="writeable">{boolean value=$label.editable}</span></td>
  <td class="centered"><span id="labelmultiple" class="writeable">{boolean value=$label.multiple}</span></td>
  <td class="centered"><span id="labeldefault" class="writeable">{boolean value=$label.default}</span></td>
  <td class="centered"><span id="labelpublic" class="writeable">{boolean value=$label.public}</span></td>
</tr>
</table>
</div>

</div>

<p>
{form_open name=form_delete to="label/delete_redirect"}
{form_hidden name=id value=$label.id}
{form_submit name=delete_button msg=Delete}
{form_end}

<script>
{to_js var=label value=$label}
{literal}
$(document).ready(function () {
  activate_delete_dialog(get_app_url() + '/label/delete_dialog/' + label.id);
});
{/literal}
</script>
{button name="browse_labels" msg="List labels" to="label/browse"}
</p>

