<h2>Tree browsing</h2>

<script>
{literal}
$(document).ready(function () {

  var base_site = get_app_url() + "/taxonomy/";
  var paging_size = {/literal}{$paging_size}{literal};

  function when_submit()
  {
    var tree = $('#tree').val();
    var obj = $('#show_data');

    obj[0].tree = tree;

    obj.grid({
      url: get_app_url() + '/taxonomy',
      total: 'total_tree_childs/' + tree,
      retrieve: 'tree_childs/' + tree,
      size: paging_size,
      fieldNames: ['Select', 'Name', 'Rank', 'Tree'],
      fields: ['select', 'name', 'rank_name', 'tree_name'],
      links: {
        name: function(row) {
            return base_site + 'view/' + row.id;
        },
      },
      dataTransform: {
        tree_name: function (row) {
          return row.tree_name == null ? "---" : row.tree_name;
        },
        select: function (row) {
          return '<a href="#" class="select_child">Go</a>';
        }
      },
      finishedFun: function (opts) {
        $('a[@class=select_child]', obj).click(function (e) {
          // get row id that contains taxonomy ID
          var tr_id = $(e.target).parent().parent()[0].id;
          var id = parse_id(tr_id);

          alert(id);
        });
      }
    });
  }

  $("#form_search").validate({submitHandler: when_submit});
  $('#show_data').gridEnable({paginate: true});
});
{/literal}
</script>

<p>
{form_open name=form_search}
{form_row type=select data=$trees name=tree msg='Tree:' blank=yes start=0}
{form_submit name=submit_search msg=Search}
{form_end}
</p>

<div id="show_data"></div>

