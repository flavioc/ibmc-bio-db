<div id="{$dom_id}"></div>

<script>
{literal}
$(function () {
  $('#{/literal}{$dom_id}{literal}')
  .gridEnable()
  .grid({
    ajax_method: 'post',
    url: get_app_url() + '/search',
    retrieve: 'get_search',
    total: 'get_search_total',
    params: {
      search: '{/literal}{$encoded}{literal}'
      {/literal}{if $transform}
      , transform: {$transform}
      {/if}{literal}
    },
    fieldNames: [{/literal}{if $add_change}'Change',{/if}{literal} 'Labels', 'Name'],
    fields: [{/literal}{if $add_change}'change',{/if}{literal} 'labels', 'name'],
    tdClass: {user_name: 'centered', labels: 'centered', update: 'centered', change: 'centered'},
    width: {
      user_name: w_user,
      update: w_update,
      change: w_add,
      labels: w_select
    },
    ordering: {
      name: 'asc',
      update: 'def',
      user_name: 'def'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      },
      labels: function (row) {
        return get_app_url() + '/sequence/labels/' + row.id;
      }
    },
    dataTransform: {
      {/literal}{if $add_change}
      {literal}
      change: function (row) {
        return img_add;
      },
      {/literal}
      {/if}{literal}
      labels: function (row) {
        return img_go;
      }
    }
    {/literal}{if $add_change}
    ,{literal}
    clickFun: {
      change: function (row) {
        if(!current_label) {
          return false;
        }
        
        var url = get_app_url() + '/change_labels/change_dialog/' + row.id + '/' + current_label.id;
        
        tb_show('Add label', url);
        return false;
      }
    }{/literal}
    {/if}{literal}
  });
});
{/literal}
</script>