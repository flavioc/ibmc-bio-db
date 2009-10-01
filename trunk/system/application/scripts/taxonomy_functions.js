
function start_tax_search_form(dom, put_addchild, click_fun)
{
  var taxonomy_name_field = $('#name');
  var taxonomy_rank_field = $('#rank');
  var taxonomy_tree_field = $('#tree');
  var taxonomy_form_search = $('#form_search');
  var form_tax_changed = false;
  var form_tax_base_site = get_app_url() + "/taxonomy/";
  var form_tax_add_child = false;
  var form_tax_field_names = ['Name', 'Rank', 'Tree'];
  var form_tax_fields = ['name', 'rank_name', 'tree_name'];
  var form_tax_click_fun = null;
  var data_tax = $(dom);

  form_tax_add_child = put_addchild;
  form_tax_click_fun = click_fun;

  if(form_tax_add_child && get_logged_in()) {
    form_tax_field_names.push('Child');
    form_tax_fields.push('add_child');
  }

  function taxonomy_form_changed()
  {
    form_tax_changed = true;
  }

  taxonomy_name_field.change(taxonomy_form_changed);
  taxonomy_rank_field.change(taxonomy_form_changed);
  taxonomy_tree_field.change(taxonomy_form_changed);
  taxonomy_rank_field.change(activate_autocomplete);

  function form_tax_when_submit()
  {
    if(form_tax_changed) {
      data_tax.gridReload();

      form_tax_changed = false;
    }
  }

  taxonomy_form_search.validate({
    rules: {
      name: {
        required: false,
        minlength: 0,
        maxlength: 512
      },
      rank: {
        required: true
      },
      tree: {
        required: true
      }
    },
    submitHandler: form_tax_when_submit,
    errorPlacement: basicErrorPlacement
  });

  function activate_autocomplete()
  {
    taxonomy_name_field.unbind('autocomplete').autocomplete(form_tax_base_site + 'search_autocomplete', {
      width: 260,
      minChars: 2,
      delay: 3000,
      scroll: true,
      selectFirst: false,
      extraParams: {
        rank: function() { return taxonomy_rank_field.val(); },
        tree: function() { return taxonomy_tree_field.val(); }
      }
    });
  }

  activate_autocomplete();

  data_tax.gridEnable();
  data_tax.grid({
      url: get_app_url() + '/taxonomy',
      total: 'search_total',
      retrieve: 'search',
      params: {
        name: function () { return taxonomy_name_field.val(); },
        rank: function () { return taxonomy_rank_field.val(); },
        tree: function () { return taxonomy_tree_field.val(); }
      },
      tdClass: {
        tree_name: 'centered',
        add_child: 'centered',
        rank_name: 'centered'
      },
      width: {
        add_child: w_add,
        tree_name: w_tree,
        rank_name: w_rank
      },
      ordering: {
        name: 'asc',
        rank_name: 'def',
        tree_name: 'def'
      },
      fieldNames: form_tax_field_names,
      fields: form_tax_fields,
      links: {
        name: function(row) {
          return form_tax_base_site + 'view/' + row.id;
        },
        tree_name: function(row) {
          return get_app_url() + '/tree/view/' + row.tree_id;
        },
        parent_name: function(row) {
          if(row.parent_id == null) {
            return null;
          } else {
            return form_tax_base_site + 'view/' + row.parent_id;
          }
        },
        add_child: function (row) {
          return get_app_url() + '/taxonomy/add?parent_id=' + row.id + '&tree=' + row.tree_id;
        }
      },
      clickFun: form_tax_click_fun,
      dataTransform: {
        parent_name: function(row) {
          return row.parent_name == null ? null : row.parent_name;
        },
        tree_name: function (row) {
          return row.tree_name == null ? null : row.tree_name;
        },
        rank_name: function (row) {
          return row.rank_name == null ? null : row.rank_name;
        },
        add_child: function (row) {
          return img_add;
        }
      },
  });
}

