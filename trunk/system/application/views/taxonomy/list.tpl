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
    fieldNames: ['Name', 'Last update', 'User', '$delete', 'Add'],
    fields: ['name', 'update', 'user_name', '$delete', 'add'],
    editables: {
      name: {
        select : true,
        submit : 'OK',
        cancel : 'cancel',
        cssclass : 'editable',
        width: '200px'
      }
    },
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
      }
    },
    countRemove: 'total_taxonomies',
    what: 'tree',
    removeAssociated: 'taxonomies',
    enableRemove: true
  });
});
</script>
{/literal}

<div id="show_trees"></div>

<p>
{button name="add_tree" to="tree/add" msg="Add tree"}
</p>
