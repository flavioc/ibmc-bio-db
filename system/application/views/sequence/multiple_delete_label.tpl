<h2>Delete label from multiple sequences</h2>

{literal}
<style>
#submit_delete_label {
  display: block;
}

#info_results {
  margin-top: 10px;
}
</style>
{/literal}

<h3>Form</h3>

{form_open to='#' name=label_form}
{form_hidden name=search value=$encoded}
{form_hidden name=transform value=$transform}
{include file=common_label/select_label.tpl}

{form_submit name='submit_delete_label' msg='Open dialog'}
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
      ,transform: {$transform}
      {/if}
      {literal}
    },
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    tdClass: {user_name: 'centered', update: 'centered'},
    width: {
      user_name: w_user,
      update: w_update
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
    }
  });
});
{/literal}
</script>