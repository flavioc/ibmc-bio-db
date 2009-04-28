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
    fieldNames: ['Name', 'Parent', 'Last update', 'User', 'Taxonomy', 'Child'],
    fields: ['rank_name', 'rank_parent_name', 'last_update', 'last_user', 'add', 'add_child'],
    tdClass: {last_update: 'centered', add: 'centered', add_child: 'centered'},
    width: {
      add: w_add,
      add_child: w_add,
      last_user: w_user,
      last_update: w_update,
      rank_name: '26%'
    },
    dataTransform: {
      add: function (row) {
        return img_add;
      },
      add_child: function (row) {
        return img_add;
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
    total: 'get_total',
    idField: 'rank_id'
  });
});
</script>
{/literal}

<div id="show_ranks"></div>

{button name="add_rank" to="rank/add" msg="Add rank"}
