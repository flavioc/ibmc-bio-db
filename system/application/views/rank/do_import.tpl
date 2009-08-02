<h2>Import ranks report</h2>

{if $ranks}
<p>The next table shows the import results:</p>

<div id="show_ranks"></div>
{literal}<script>
$(function () {
  $('#show_ranks')
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$ranks}{literal},
    
    fieldNames: ['Success', 'Mode', 'Name', 'Parent found', 'Original Parent', 'Parent'],
    fields: ['success', 'mode', 'rank_name', 'parent_found', 'original_parent', 'rank_parent_name'],
    tdClass: {
      success: 'centered',
      mode: 'centered',
      parent_found: 'centered'
    },
    width: {
      rank_name: '26%',
      success: w_boolean,
      mode: w_boolean,
      parent_found: w_boolean
    },
    types: {
      success: 'boolean',
      parent_found: 'boolean'
    },
    dataTransform: {
      success: function (row) {
        return row.id > 0;
      },
      rank_parent_name: function (row) {
        if(row.parent_found) {
          return row.rank_parent_name;
        }
        
        return null;
      },
      original_parent: function (row) {
        return row.rank_parent_name;
      }
    },
    links: {
      rank_name: function (row) {
        return get_app_url() + '/rank/view/' + row.id;
      },
      rank_parent_name: function (row) {
        if(row.parent_found) {
          return get_app_url() + '/rank/view/' + row.parent_id;
        } else {
          return null;
        }
      }
    }
  });
});
</script>{/literal}

{else}
<p>No ranks were imported.</p>
{/if}