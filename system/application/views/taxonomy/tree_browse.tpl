<h2>Tree browsing</h2>

<script>
{literal}

$(document).ready(function () {

  var base_site = get_app_url() + "/taxonomy/";
  var parents = {};

  function add_path(path, what, tax, tree)
  {
    return path.concat([{what: what, tax: tax, tree: tree}]);
  }

  function show_path(obj, path)
  {
    var dom_path = $('#tax_path');

    if(path.length == 0) {
      dom_path.hide();
    } else {
      dom_path.empty();

      for(var i = 0; i < path.length; ++i) {
        var elem = path[i];

        if(i > 0) {
          dom_path.append(' > ');
        }

        dom_path.append('<a class="clickable" href="#">' + elem.what + '</a>');
      }

      $('a', dom_path).each(function (index) {
        var elem = path[index];

        $(this).click(function (event) {
          reload_grid(obj, elem.tree, elem.tax, elem.what, path.slice(0, index));
          
          return false;
        });
      });

      dom_path.show();
    }
  }

  function reload_grid(obj, tree, tax, name, path)
  {
    var add_child = $('#add_child');
    if(name == '') {
      name = '---';
    }
    var new_path = add_path(path, name, tax, tree);
  
    add_child.hide();

    obj.grid({
      url: get_app_url() + '/taxonomy',
      total: 'total_taxonomy_children/' + tax + '/' + tree,
      retrieve: 'taxonomy_children/' + tax + '/' + tree,
      fieldNames: ['Select', 'Name', 'Rank', 'Tree', 'Child'],
      fields: ['select', 'name', 'rank_name', 'tree_name', 'add_child'],
      tdClass: {
        select: 'centered',
        tree_name: 'centered',
        add_child: 'centered',
        rank_name: 'centered'
      },
      width: {
        add_child: w_add,
        tree_name: w_tree,
        select: w_select,
        rank_name: w_rank
      },
      links: {
        name: function(row) {
          return base_site + 'view/' + row.id;
        },
        add_child: function (row) {
          return base_site + 'add?parent_id=' + row.id + '&tree=' + row.tree_id;
        },
        rank_name: function (row) {
          return get_app_url() + '/rank/view/' + row.rank_id;
        }
      },
      dataTransform: {
        tree_name: function (row) {
          return row.tree_name == null ? "---" : row.tree_name;
        },
        select: function (row) {
          return img_go;
        },
        add_child: function (row) {
          return img_add;
        }
      },
      clickFun: {
        select: function (row) {
          var id = row.id;
          var new_name = row.name;

          parents[id] = {id: tax, name: name};

          reload_grid(obj, tree, id, new_name, new_path);
        
          return false;
        }
      },
      finishedFun: function (opts) {
        var childs_name = $('#childs_name');
        var go_up = $('#go_up');

        add_child.attr('href', get_app_url() + '/taxonomy/add?parent_id=' + tax + '&tree=' + tree);
        add_child.show();

        if(tax == 0) {
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
            reload_grid(obj, tree, parent_tax, parent_name, new_path.slice(0, new_path.length-2));
            return false;
          });
          go_up.show();
        }

        show_path(obj, new_path);
        childs_name.show();
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

    reload_grid(obj, tree, tax, tree_name, []);
  }

  $('#childs_name').hide();
  $('#go_up').hide();
  $('#add_child').hide();
  $('#tax_path').hide();
  $("#form_search").validate({submitHandler: when_submit});
  $('#show_data').gridEnable({paginate: true});
});
{/literal}
</script>

{if $start_tree}{literal}<script>
$(function () {
  $('#form_search').submit();
});
</script>{/literal}{/if}

<p>
{form_open name=form_search}
{form_row type=select data=$trees name=tree msg='Tree:' blank=yes start=$start_tree}
{form_submit name=submit_search msg=Search}
{form_end}
</p>

<h3 id="childs_name"></h3>
<p class="path" id="tax_path"></p>
<a class="clickable" id="go_up" href="#">Go up <span id="go_up_what"></span></a>
<div id="show_data"></div>
<br />
<a class="clickable" id="add_child" href="#">Add child</a>

