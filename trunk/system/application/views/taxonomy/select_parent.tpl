
<h2>Select taxonomy's parent</h2>

{form_open name=form_select_parent to="taxonomy/set_parent"}

<fieldset>

{form_row name=tax msg="Taxonomy:" readonly=readonly}
{form_hidden name=hidden_tax}
{form_hidden name=tax_id value=$taxonomy}

</fieldset>

{form_submit name=submit_file msg='Select'}
{form_end}

<p>
<hr />
<br />

<div id="taxonomy_search">
  {include file=taxonomy/form_search.tpl}

  <div id="show_data"></div>

  <script>
  {literal}
  $(document).ready(function () {
    var place = $('#show_data');
    var form = $('#form_select_parent');

    form.validate({
      rules: {
        tax: {
          required: true
        }
      }
    });

    start_tax_search_form('#show_data', false,
      {
        name: function (row) {
          var input_show = $('input[name=tax]', form);
          var input_hide = $('input[name=hidden_tax]', form);

          input_show.attr('value', row.name);
          input_hide.attr('value', row.id);

          place.gridHighLight(row.id);
        }
      });
  });
  {/literal}
</script>

</div>
</p>

