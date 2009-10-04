<h2>View label</h2>

{if label_special_purpose($label.name) }
<p>The label <em>{$label.name}</em> is special purpose and cannot be viewed/edited.</p>

{else}

{if $label.default}
<p>The label <em>{$label.name}</em> is system default and cannot be changed.</p>
{/if}

{if $is_admin && !$label.default}
  <script>
  {to_js var=rank value=$rank}
  {to_js var=label value=$label}
  {literal}
  $(function () {
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
      type: 'select',
      submit: 'OK',
      cancel: 'cancel',
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
      submitdata: senddata,
      loadurl: base_site + 'get_code/' + label.id
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
      submitdata: senddata,
      loadurl: base_site + 'get_validcode/' + label.id
    });
    
    $('#labelactionmodification').editable(base_site + 'edit_actionmodification', {
      select: true,
      submit: 'OK',
      cancel: 'cancel',
      width: '150px',
      style: 'inherit',
      type: 'textarea',
      cols: 70,
      rows: 15,
      submitdata: senddata,
      loadurl: base_site + 'get_actionmodification/' + label.id
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
      submitdata: senddata,
      loadurl: base_site + 'get_comment/' + label.id
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
{/if}

{literal}
<style type="text/css">
#labelcomment, #labelcode, #labelvalidcode, #labelactionmodification {
  margin-left: 180px;
  width: 50%;
}
</style>
{/literal}

<div class="data_show">
  <p><span class="desc">Name: </span><span id="labelname">{$label.name}</span></p>
  <p><span class="desc">Type: </span><span id="labeltype">{$label.type}</span></p>
  
  <p><span class="desc">Code: </span></p>
  <div class="code" id="labelcode">{if $label.code}{$label.code}{else}---{/if}</div>
  
  <p><span class="desc">Validation code: </span></p>
  <div class="code" id="labelvalidcode">{if $label.valid_code}{$label.valid_code}{else}---{/if}</div>
  
  <p><span class="desc">Modification code: </span></p>
  <div class="code" id="labelactionmodification">{if $label.action_modification}{$label.action_modification}{else}---{/if}</div>
  
  <p><span class="desc">Comment: </span></p>
  <div id="labelcomment">{if $label.comment}{$label.comment}{else}---{/if}</div>

{include file='history/form_view.tpl' data=$label}

<div class="grid_form">
<h3>Label flags</h3>
<table class="data fixed_table">
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
  <td class="centered"><span id="labelmust_exist">{boolean value=$label.must_exist}</span></td>
  <td class="centered"><span id="labelauto_on_creation">{boolean value=$label.auto_on_creation}</span></td>
  <td class="centered"><span id="labelauto_on_modification">{boolean value=$label.auto_on_modification}</span></td>
  <td class="centered"><span id="labeldeletable">{boolean value=$label.deletable}</span></td>
  <td class="centered"><span id="labeleditable">{boolean value=$label.editable}</span></td>
  <td class="centered"><span id="labelmultiple">{boolean value=$label.multiple}</span></td>
  <td class="centered"><span id="labeldefault">{boolean value=$label.default}</span></td>
  <td class="centered"><span id="labelpublic">{boolean value=$label.public}</span></td>
</tr>
</table>
</div>

{if $is_admin && !$label.default}
  {literal}<script>
  $(function () {
    $('td span, #labelname, #labeltype, #labelcode, #labelvalidcode, #labelactionmodification, #labelcomment').addClass('writeable');
  });
  </script>{/literal}
{/if}

</div>

{if $is_admin && !$label.default}
  {form_open name=form_delete to="label/delete_redirect"}
  {form_hidden name=id value=$label.id}
  {form_submit name=delete_button msg=Delete}
  {form_end}
{/if}

{form_open name=form_export to="label/export_id"}
{form_hidden name=id value=$label.id}
{form_submit name=export_button msg=Export}
{form_end}

{button name="browse_labels" msg="List labels" to="label/browse"}

{if $is_admin && !$label.default}
  <script>
  {literal}
  $(function () {
    activate_delete_dialog(get_app_url() + '/label/delete_dialog/' + label.id);
  });
  </script>
  {/literal}
{/if}

{literal}
<style>
#form_export, #form_delete, #form_browse_labels {
  display: inline;
}
</style>
{/literal}

{/if}