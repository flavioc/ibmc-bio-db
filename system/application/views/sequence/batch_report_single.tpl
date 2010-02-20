<p>The imported sequences can be <a href="{site}/search?type=batch" id="batch_manipulate_{$what}">batch manipulated</a>.</p>

{literal}<script>
$(function () {
  $('#batch_manipulate_{/literal}{$what}{literal}').click(function () {
    $.cookie('saved_search_tree', $.toJSON({/literal}{$search_tree_get}{literal}, true), cookie_options);
    return true;
  });
})
</script>{/literal}

{if $labels && count($labels) > 0}
<h3>Labels</h3>
<table class="data">
  <tr>
    <th style="width: 20%;">Label</th>
    <th style="width: 20%;">Type</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels key=name item=label}
  <tr>
    <td class="centered">{$name}</td>
    <td class="centered">{if $label.type}{$label.type}{else}---{/if}</td>
    <td>{if $label.status == 'not_found'}Label is NOT INSTALLED{elseif $label.status == 'ok'}OK{elseif $label.status == 'base'}OK: Base label{/if}</td>
  </tr>
  {/foreach}
</table>
<br />

{if $empty && count($empty) > 0}
<h4>Empty sequences</h4>

The following sequences were empty:

<ul>
  {foreach from=$empty item=emp}
    <li>{$emp}</li>
  {/foreach}
</ul>
{/if}

{if $error && count($error) > 0}
<h4>Unexpected lines</h4>

The following lines contained errors:

<ul>
  {foreach from=$error item=e}
    <li>{$e}</li>
  {/foreach}
</ul>
{/if}

{foreach from=$sequences item=seq}
<div id="seq_{$seq.id}" {display_none}>

{if $seq.labels}
{assign var=labelsseq value=$seq.labels}
<br />
<table class="data">
  <tr>
    <th style="width: 20%;">Label</th>
    <th>Status</th>
  </tr>
  {foreach from=$labels key=name item=label}
    {if $labelsseq[$name]}
      <tr>
        <td>{$name}</td>
        <td>{$labelsseq[$name].status}</td>
      </tr>
    {/if}
  {/foreach}
</table>
{else}
<p>No labels for this sequence.</p>
{/if}
</div>

{/foreach}
{/if}

{literal}<script>
$(function () {
  var grid = $('#show_seqs_{/literal}{$what}{literal}');
  var fieldNames = ['New'];
  var fields = ['add'];
  
  {/literal}{if count($labels) > 0}fieldNames.push('Status');
  fields.push('status');{/if}{literal}
  
  $.merge(fieldNames, ['Name', 'Content', 'Comment', 'Labels']);
  $.merge(fields, ['name', 'short_content', 'comment', 'labels']);
  
  grid
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$sequences}{literal},
    fieldNames: fieldNames,
    fields: fields,
    tdClass: {
      add: 'centered',
      labels: 'centered',
      status: 'centered'
    },
    ordering: {
      add: 'def',
      name: 'def'
    },
    dataTransform: {
      labels: function (row) {
        return img_go;
      },
      status: function (row) {
        return img_go;
      },
      short_content: function (row) {
        return row.short_content + '...';
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
    },
    clickFun: {
      status: function (row) {
        tb_show('Sequence status for ' + row.name, '#TB_inline?inlineId=seq_' + row.id + '&width=700&height=300');
        return false;
      }
    }
  });
});
</script>{/literal}

<div id="show_seqs_{$what}">
</div>