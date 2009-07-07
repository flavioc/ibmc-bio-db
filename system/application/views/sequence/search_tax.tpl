<div id="taxonomy_search">
  {include file=taxonomy/form_search.tpl}

  <div id="show_data"></div>

  <script>
  {literal}
  $(document).ready(function () {
    var place = $('#show_data');

    start_tax_search_form('#show_data', false,
      {
        name: function (row) {
          var data = $('#data_tax');

          place.gridHighLight(row.id);
          data.text(row.name);

          data[0].tax = row;

          tb_remove();
        }
      });
  });
  {/literal}
</script>

</div>
</p>
