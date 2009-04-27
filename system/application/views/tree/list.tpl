<h2>Tree list</h2>

{literal}
<script>
  $(document).ready(function () {

  var base_site = get_app_url() + '/tree';

  $('#show_trees')
  .gridEnable({paginate: false})
  .grid({
    url: base_site,
    retrieve: 'get_all',
    fieldNames: ['Name', 'Last update', 'User', 'Add'],
    fields: ['name', 'update', 'user_name', 'add'],
    dataTransform: {
      add: function (row) {
        return 'Add';
      }
    },
    links: {
      add: function (row) {
        return get_app_url() + '/taxonomy/add?tree=' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      },
      name: function (row) {
        return base_site + '/view/' + row.id;
      }
    },
    tdClass: {update: 'centered', add: 'centered'},
    width: {
      add: w_add,
      user_name: w_user,
      update: w_update
    }
  });
});
</script>
{/literal}

<div id="show_trees"></div>

{button name="add_tree" to="tree/add" msg="Add tree"}
