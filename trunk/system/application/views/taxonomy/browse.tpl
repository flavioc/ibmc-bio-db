<h2>{$subtitle}</h2>

<script>
{literal}
$(document).ready(function () {

  var base_site = get_app_url() + "/taxonomy/";
  var paging_size = {/literal}{$paging_size}{literal};
  var changed = true;

  function when_submit()
  {
    if(changed) {
      $('#show_data').grid({
        url: get_app_url() + '/taxonomy',
        total: 'search_total',
        retrieve: 'search',
        size: paging_size,
        params: {
          name: function () { return $('#name').val(); },
          rank: function () { return $('#rank').val(); },
          tree: function () { return $('#tree').val(); }
        },
        fieldNames: ['Name', 'Rank', 'Tree', 'Parent', 'Add child'],
        fields: ['name', 'rank_name', 'tree_name', 'parent_name', 'add_child'],
        links: { name: function(row) {
          {/literal}
          {if $child_id}
            return base_site + 'set_parent/{$child_id}/' + row.id;
          {else}
            return base_site + 'view/' + row.id;
          {/if}
          {literal}
          },
          parent_name: function(row) {
            if(row.parent_id == null) {
              return null;
            } else {
              return base_site + 'view/' + row.parent_id;
            }
          },
          add_child: function (row) {
            return get_app_url() + '/taxonomy/add?parent_id=' + row.id + '&tree=' + row.tree_id;
          }
        },
        dataTransform: {
          parent_name: function(row) {
            return row.parent_name == null ? "-" : row.parent_name;
          },
          tree_name: function (row) {
            return row.tree_name == null ? "---" : row.tree_name;
          },
          rank_name: function (row) {
            return row.rank_name == null ? "---" : row.rank_name;
          },
          add_child: function (row) {
            return 'Add';
          }
        }
      });

      changed = false;
    }
  }

  function when_changing()
  {
    changed = true;
  }

  $('#name, #rank').change(when_changing);

  $('#rank').change(activate_autocomplete);

  $("#form_search").validate({
    rules: {
      name: {
        required: true,
        minlength: 2,
        maxlength: 512
      },
      rank: {
        required: true
      }
    },
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  function activate_autocomplete()
  {
    $("#name").unbind('autocomplete').autocomplete(base_site + "search_autocomplete", {
      width: 260,
      minChars: 2,
      delay: 3000,
      scroll: true,
      selectFirst: false,
      extraParams: {
        rank: function() { return $('#rank').val(); },
        tree: function() { return $('#tree').val(); }
      }
    });
  }

  activate_autocomplete();

  $('#show_data').gridEnable();
});
{/literal}
</script>

<p>
{form_open name=form_search}
{form_row name=name msg='Name:'}
{form_row type=select data=$ranks name=rank msg='Rank:' blank=yes start=0 key=rank_id value=rank_name}
{form_row type=select data=$trees name=tree msg='Tree:' blank=yes start=0}
{form_submit name=submit_search msg=Search}
{form_end}
</p>

<div id="show_data"></div>

{if $child_id}
<p>Search for the parent's taxonomy name and then click on the name to apply changes</p>
{/if}

