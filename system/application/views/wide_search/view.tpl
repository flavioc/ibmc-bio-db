<h2>Wide search by "{$search}"</h2>

<script>
var default_search_size = 3;
{literal}
$(function () {
  $('h3').effect('highlight', {}, 3000);
  $('#error_search').effect('highlight', {color: 'red'}, 2000);
});
{/literal}
</script>

<p id="warning_search">The search string provided was not valid (<span id="error_search">{$error}</span>)</p>

{if $sequences}
<h3>Sequences</h3>
<div id="sequence_box"></div>

{literal}<script>
$(function () {
  $('#sequence_box')
  .gridEnable()
  .grid({
    size: default_search_size,
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    total: 'get_total',
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    tdClass: {
      update: 'centered',
      user_name: 'centered'
    },
    width: {
      update: '30%',
      user_name: w_user
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    params: {
      name: "{/literal}{$search}{literal}"
    }
  });
});
</script>{/literal}
{/if}

{if $labels}
<h3>Labels</h3>
<div id="labels_box"></div>
{literal}<script>
$(function () {
  $('#labels_box')
  .gridEnable()
  .grid({
    size: default_search_size,
    url: get_app_url() + '/label',
    retrieve: 'get_all',
    total: 'count_total',
    fieldNames: ['Name', 'Type', 'Last update', 'User'],
    fields: ['name', 'type', 'update', 'user_name'],
    params: {
      name: "{/literal}{$search}{literal}"
    },
    links: {
      name: function (row) {
        return get_app_url() + '/label/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    width: {
      type: '20%',
      update: '30%',
      user_name: w_user
    },
    tdClass: {
      update: 'centered',
      user_name: 'centered'
    },
  });
});
</script>{/literal}
{/if}

{if $taxonomies}
<h3>Taxonomies</h3>
<div id="taxonomy_box"></div>
{literal}<script>
$(function () {
  $('#taxonomy_box')
  .gridEnable()
  .grid({
      url: get_app_url() + '/taxonomy',
      total: 'search_total',
      retrieve: 'search',
      size: default_search_size,
      params: {
        name: "{/literal}{$search}{literal}"
      },
      tdClass: {
        tree_name: 'centered',
        rank_name: 'centered'
      },
      width: {
        tree_name: w_tree,
        rank_name: w_rank
      },
      fieldNames: ['Name', 'Rank', 'Tree'],
      fields: ['name', 'rank_name', 'tree_name'],
      links: {
        tree_name: function(row) {
          return get_app_url() + '/tree/view/' + row.tree_id;
        },
        name: function(row) {
          return get_app_url() + '/taxonomy/view/' + row.id;
        }
      },
      dataTransform: {
      },
  });
});
</script>{/literal}
{/if}

{if $ranks}
<h3>Ranks</h3>
<div id="ranks_box"></div>
{literal}<script>
$(function () {
  $('#ranks_box')
  .gridEnable()
  .grid({
    url: get_app_url() + '/rank',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Parent', 'Last update', 'User'],
    fields: ['rank_name', 'rank_parent_name', 'update', 'user'],
    tdClass: {
      update: 'centered',
      user: 'centered'
    },
    params: {
      name: "{/literal}{$search}{literal}"
    },
    width: {
      last_user: w_user,
      update: w_update,
      rank_name: '26%'
    },
    dataTransform: {
      user: function (row) {
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
      user: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    },
    total: 'get_total',
    idField: 'rank_id',
    size: default_search_size
  });
});
</script>{/literal}
{/if}

{if !$sequence && !$labels && !$taxonomies && !$ranks}
<p>Nothing was found.</p>
{/if}