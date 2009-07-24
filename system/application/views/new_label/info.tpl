{include file=common_label/info.tpl}

<script>
{to_js var=label value=$label}

{literal}
$(function () {
  $('#form_add_label').ajaxFormAdd();
});
{/literal}
</script>
