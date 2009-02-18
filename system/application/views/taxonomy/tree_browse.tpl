<h2>Tree browsing</h2>

<script>
{literal}
$(document).ready(function () {

  var base_site = get_app_url() + "/taxonomy/";
  var paging_size = {/literal}{$paging_size}{literal};
  var parents = {};

  function reload_grid(obj, tree, tax, name)
  {
    obj.grid({
      url: get_app_url() + '/taxonomy',
      total: 'total_taxonomy_childs/' + tax + '/' + tree,
      retrieve: 'taxonomy_childs/' + tax + '/' + tree,
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
        var childs_name = $('#childs_name');
        var go_up = $('#go_up');

        if(tax == 0) {
          if(name == '') {
            name = '---';
          }
          childs_name.text('Roots for tree ' + name);
          go_up.hide();
        } else {
          childs_name.html('Children of taxonomy <a href="' + get_app_url() + '/taxonomy/view/' + tax + '">' + name + '</a>');

          // get parent info
          var parent_info = parents[tax];
          var parent_tax = parent_info.id;
          var parent_name = parent_info.name;

          $('#go_up_what').text(parent_name);
          go_up
          .unbind('click')
          .click(function () {
            reload_grid(obj, tree, parent_tax, parent_name);
          });
          go_up.show();
        }

        childs_name.show();

        $('a[@class=select_child]', obj).click(function (e) {
          // get row id that contains taxonomy ID
          var tr_id = $(e.target).parent().parent()[0].id;
          var id = parse_id(tr_id);

          // get taxonomy name
          var new_name = $(e.target).parent().next().children()[0].innerHTML;

          parents[id] = {id: tax, name: name};

          reload_grid(obj, tree, id, new_name);
        });
      }
    });
  }

  function when_submit()
  {
    var tree_dom = $('#tree');
    var tree = tree_dom.val();
    var tree_name = $('#tree :selected').text();
    var obj = $('#show_data');
    var tax = 0;

    reload_grid(obj, tree, tax, tree_name);
  }

  $('#childs_name').hide();
  $('#go_up').hide();
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

<h3 id="childs_name"></h3>
<a id="go_up" href="#">Go up <span id="go_up_what"></span></a>
<div id="show_data"></div>

