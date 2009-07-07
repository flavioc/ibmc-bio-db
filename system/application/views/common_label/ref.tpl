{literal}
<script>
$(document).ready(function () {

  var form = $('#{/literal}{$form}{literal}');
  var place = $('#show_sequences');

  form.validate({
    rules: {
      ref: {
        required: generate_disabled
      }
    }
  });

  start_sequence_grid(place, {
      name: function (row) {
        var input_show = $('input[name=ref]', form);
        var input_hide = $('input[name=hidden_ref]', form);

        input_show.attr('value', row.name);
        input_hide.attr('value', row.id);

        place.gridHighLight(row.id);
      }
  });

});
</script>
{/literal}
