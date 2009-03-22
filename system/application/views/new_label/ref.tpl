
<h2>New reference label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_ref_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide="#show_sequences, #data_area, #sequence_help"}

<span id="data_area">
{form_row readonly=readonly name=ref msg='Sequence:'}
{form_hidden name=hidden_ref}
</span>

<p id="sequence_help">Please select a sequence below:</p>
<div id="show_sequences"></div>

</fieldset>

{form_submit name=submit_file msg='Add label'}
{form_end}

{literal}
<script>
$(document).ready(function () {

  var form = $('#form_add_label');
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
