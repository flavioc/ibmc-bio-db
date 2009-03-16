
<h2>New taxonomy label</h2>

{include file=new_label/info.tpl}

{form_open name=form_add_label to="label_sequence/add_tax_label"}

<fieldset>
{include file=new_label/hidden.tpl}
{include file=new_label/generate.tpl to_hide="#data_area, #taxonomy_search"}
<span id="data_area">
{form_row name=tax msg="Taxonomy:" readonly=readonly}
{form_hidden name=hidden_tax}
</span>
</fieldset>

{form_submit name=submit_file msg='Add label'}
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
    var paging_size = get_paging_size();
    var place = $('#show_data');
    var form = $('#form_add_label');

    start_tax_search_form('#show_data', paging_size, false,
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
