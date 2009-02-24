
<ul>
  <li>Name: {$label.name}</li>
  <li>Sequence name: {$sequence.name}</li>
</ul>

{literal}
<script>
$(document).ready(function () {
  function checkResponse(responseText, statusText) {
    var resp = $.evalJSON(responseText);

    if(resp == true) {
      {/literal}
      {if !$label.multiple}
      reload_addable_list();
      {/if}
      {if $label.must_exist}
      reload_missing_list();
      {/if}
      {literal}
      reload_labels_list();
    } else {
      alert(responseText);
    }

    self.parent.tb_remove();
  }

  $('#form_add_label').ajaxForm({
    success: checkResponse
  });
});
</script>
{/literal}
