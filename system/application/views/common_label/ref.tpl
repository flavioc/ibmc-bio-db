{literal}
<script>
$(function () {
  var form = $('#{/literal}{$form}{literal}');
  var place = $('#show_sequences');

  form.validateRefLabel();
  start_sequence_grid(place, { name: change_label_ref(place, form) });
  activate_sequence_search(place);
});
</script>
{/literal}

<hr />

{include file=sequence/form_search.tpl}
<div id="show_sequences"></div>
