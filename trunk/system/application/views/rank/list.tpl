<h2>Rank list</h2>

{literal}
<script>
  $(document).ready(function () {

  var base_site = '{/literal}{site}{literal}/rank/';
  var paging_size = {/literal}{$paging_size}{literal};

  $('#show_ranks')
  .gridEnable()
  .grid({
    url: get_app_url() + '/rank',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Parent', '$delete', 'Add taxonomy', 'Add child rank'],
    fields: ['rank_name', 'rank_parent_name', '$delete', 'add', 'add_child'],
    size: paging_size,
    dataTransform: {
      add: function (row) {
        return 'Add';
      },
      add_child: function (row) {
        return 'Add';
      },
      rank_parent_name: function (row) {
        if (row.rank_parent_name == null) {
          return "---";
        } else {
          return row.rank_parent_name;
        }
      }
    },
    links: {
      rank_name: function (row) {
        return get_app_url() + '/rank/view/' + row.rank_id;
      },
      add: function (row) {
        return get_app_url() + '/taxonomy/add?rank=' + row.rank_id;
      },
      add_child: function (row) {
        return get_app_url() + '/rank/add?parent_id=' + row.rank_id;
      }
    },
    countRemove: 'total_taxonomies',
    what: 'rank',
    removeAssociated: 'taxonomies',
    enableRemove: true,
    total: 'get_total',
    idField: 'rank_id'
  });
});
</script>
{/literal}

<div id="show_ranks"></div>

