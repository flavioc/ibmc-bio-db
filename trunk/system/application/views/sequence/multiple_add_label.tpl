{if $mode == 'add'}
<h2>Add label to multiple sequences</h2>
{elseif $mode == 'edit'}
<h2>Edit label in multiple sequences</h2>
{/if}

<h3>Form</h3>

{form_open to='#' name=label_form}
{form_hidden name=search value=$encoded}
{form_hidden name=transform value=$transform}

{include file=common_label/select_label.tpl}

{if $mode == 'add'}
{form_row type=checkbox name=update msg="Update:"}
{/if}

{if $mode == 'edit'}
{form_row type=checkbox name=addnew msg='Add new:'}
{/if}

{form_row type=checkbox name=multiple msg="Add multiple:"}
{form_submit name='submit_add_label' msg='Open dialog'}
{form_end}

<div id="info_results"></div>

<h3>Sequences</h3>

<div id="sequence_list"></div>

<script>
{literal}
$(function () {
  $('#sequence_list')
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_search',
    total: 'get_search_total',
    params: {
      search: '{/literal}{$encoded}{literal}'
      {/literal}
      {if $transform}
      , transform: {$transform}
      {/if}
      {literal}
    },
    fieldNames: ['Change', 'Name', 'Last update', 'User'],
    fields: ['change', 'name', 'update', 'user_name'],
    tdClass: {user_name: 'centered', update: 'centered', change: 'centered'},
    width: {
      user_name: w_user,
      update: w_update,
      change: w_add
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
      }
    },
    dataTransform: {
      change: function (row) {
        return img_add;
      }
    },
    clickFun: {
      change: function (row) {
        if(!current_label) {
          return false;
        }
        
        var url = get_app_url() + '/change_labels/change_dialog/' + row.id + '/' + current_label.id;
        
        tb_show('Add label', url);
        return false;
      }
    }
  });
});
{/literal}
</script>