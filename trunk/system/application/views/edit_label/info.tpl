{include file=common_label/info.tpl}

<script>
{to_js var=label value=$label}
{literal}
$(function () {
  $('#form_edit_label').ajaxFormEdit();
});
{/literal}
</script>