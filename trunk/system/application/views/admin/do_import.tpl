<h2>Import database results</h2>

<h3>Imported labels</h3>
{include file=label/import_results.tpl}

{if $ranks}
<h3>Imported ranks</h3>

{include file=rank/common_import.tpl}
{/if}

<h3>Imported taxonomy trees</h3>

{if $trees && !empty($trees)}

{literal}<script>
$(function () {
  var grid = $('#show_trees');
  
  grid
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$trees}{literal},
    fieldNames: ['Mode', 'Name', 'New ranks', 'New taxonomies', 'Old taxonomies'],
    fields: ['mode', 'name', 'new_ranks', 'new_tax', 'old_tax'],
    tdClass: {
      mode: 'centered',
      new_ranks: 'centered',
      new_tax: 'centered',
      old_tax: 'centered'
    },
    dataTransform: {
      mode: function (row) {
        if(row.mode == 'edit') {
          return 'Edit';
        } else {
          return 'Add';
        }
      }
    },
    width: {
      mode: w_int,
      new_ranks: w_int,
      new_tax: '17%',
      old_tax: '15%'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/tree/view/' + row.id;
      }
    }
  });
});
</script>{/literal}
    
<div id="show_trees">
</div>

{else}
<p>No trees were imported.</p>
{/if}

<h3>Imported sequences</h3>

{if $sequences && !empty($sequences)}
{literal}<script>
$(function () {
  var grid = $('#show_seqs');
  
  grid
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$sequences}{literal},
    fieldNames: ['New', 'Name', 'Content', 'Labels'],
    fields: ['add', 'name', 'short_content', 'labels'],
    tdClass: {
      add: 'centered',
      labels: 'centered'
    },
    dataTransform: {
      labels: function (row) {
        return img_go;
      },
      short_content: function (row) {
        if(row.content != row.short_content) {
          return row.short_content + '...';
        } else {
          return row.short_content;
        }
      }
    },
    types: {
      add: 'boolean'
    },
    width: {
      add: w_boolean,
      labels: '10%'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      labels: function (row) {
        return get_app_url() + '/sequence/labels/' + row.id;
      }
    }
  });
});
</script>{/literal}
    
<div id="show_seqs">
</div>
{else}
<p>No sequences were imported.</p>
{/if}