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

  place
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    total: 'get_total',
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    clickFun: {
      name: function (row) {
        var input_show = $('input[name=ref]', form);
        var input_hide = $('input[name=hidden_ref]', form);

        input_show.attr('value', row.name);
        input_hide.attr('value', row.id);

        place.gridHighLight(row.id);
      }
    }
  });

});
</script>
{/literal}
