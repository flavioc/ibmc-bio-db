<h2>Rank list</h2>

{literal}
<script>
  $(document).ready(function () {

  var base_site = '{/literal}{site}{literal}/rank/';

  $('#show_ranks')
  .gridEnable()
  .grid({
    url: get_app_url() + '/rank',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Parent', 'Last update', 'User', 'Add taxonomy', 'Add child rank'],
    fields: ['rank_name', 'rank_parent_name', 'last_update', 'last_user', 'add', 'add_child'],
    dataTransform: {
      add: function (row) {
        return 'Add';
      },
      add_child: function (row) {
        return 'Add';
      },
      rank_parent_name: function (row) {
        if (row.rank_parent_name == null) {
          return null;
        } else {
          return row.rank_parent_name;
        }
      },
      last_update: function (row) {
        if(row.update == null) {
          return null;
        } else {
          return row.update;
        }
      },
      last_user: function (row) {
        if(row.update_user_id == null) {
          return null;
        } else {
          return row.user_name;
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
      },
      last_user: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    what: 'rank',
    total: 'get_total',
    idField: 'rank_id'
  });
});
</script>
{/literal}

<div id="show_ranks"></div>

{button name="add_rank" to="rank/add" msg="Add rank"}
