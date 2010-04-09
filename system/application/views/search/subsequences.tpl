<h2>Generated {$label.name} subsequences</h2>

{if $failure}
<p>Could not generate any sub sequences.</p>
<p>The input sequence list does not contain {$label.name} label instances or the positions are not compatible with sequence contents.</p>
{else}
<p>The system could generate the following sequences using {$label.name} label instances.</p>
<p>You can access those sequences in the search screen by clicking <a href="{site}/search?type=sub" id="search_sub">here</a>.</p>

{literal}<script>
$(function () {
  $('#search_sub').click(function () {
    $.cookie('saved_search_tree', $.toJSON({/literal}{$encoded_sub}{literal}, true), cookie_options);
    return true;
  });
});
</script>{/literal}

{include file=search/operation_sequences.tpl encoded=$encoded_sub dom_id=sub_list}
{/if}

{if $failed && count($failed) > 0}
<h3>Failed sequences</h3>

<div id="failed_seqs"></div>
{literal}<script>
$(function () {
  $('#failed_seqs')
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$failed}{literal},
    fields: ['name', 'position'],
    fieldNames: ['Name', 'Position'],
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/labels/' + row.id;
      }
    }
  });
});
</script>{/literal}
{/if}

<h3>Input sequence list</h3>

{include file=search/operation_sequences.tpl encoded=$encoded_input transform=$transform_input dom_id=input_list}
