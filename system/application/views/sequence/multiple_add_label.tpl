<h2>Add label to multiple sequences</h2>

<h3>Form</h3>

{form_open to='#' name=label_form}
{form_hidden name=search value=$encoded}
{form_row name=label msg='Label:' row_id=label_row}<span id="selected_label"></span>
{form_row type=checkbox name=update msg="Update:"}
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
    },
    fieldNames: ['Add', 'Name', 'Last update', 'User'],
    fields: ['add', 'name', 'update', 'user_name'],
    tdClass: {user_name: 'centered', update: 'centered', add: 'centered'},
    width: {
      user_name: w_user,
      update: w_update,
      add: w_add
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
      add: function (row) {
        return img_add;
      }
    },
    clickFun: {
      add: function (row) {
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
