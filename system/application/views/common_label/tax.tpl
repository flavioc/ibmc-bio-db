<hr />
<div id="taxonomy_search">
  {include file=taxonomy/form_search.tpl}

  <div id="show_data"></div>

  <script>
  {literal}
  $(function () {
    var place = $('#show_data');
    var form = $('#{/literal}{$form}{literal}');

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