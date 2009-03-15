
var form_tax_changed = false;
var form_tax_paging_size = null;
var form_tax_base_site = get_app_url() + "/taxonomy/";
var form_tax_dom = null;
var form_tax_add_child = false;
var form_tax_field_names = ['Name', 'Rank', 'Tree', 'Parent'];
var form_tax_fields = ['name', 'rank_name', 'tree_name', 'parent_name'];

function start_tax_search_form(dom, paging_size, put_addchild)
{
  form_tax_paging_size = paging_size;
  form_tax_dom = dom;
  form_tax_add_child = put_addchild;

  if(form_tax_add_child) {
    form_tax_field_names.push('Add child');
    form_tax_fields.push('add_child');
  }

  $('#name, #rank').change(function () { form_tax_changed = true; });

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
    submitHandler: form_tax_when_submit,
    errorPlacement: basicErrorPlacement
  });

  function activate_autocomplete()
  {
    $("#name").unbind('autocomplete').autocomplete(form_tax_base_site + "search_autocomplete", {
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

  $(form_tax_dom).gridEnable();
}

function form_tax_when_submit()
{
  if(form_tax_changed) {
    $(form_tax_dom).grid({
        url: get_app_url() + '/taxonomy',
        total: 'search_total',
        retrieve: 'search',
        size: form_tax_paging_size,
        params: {
          name: function () { return $('#name').val(); },
          rank: function () { return $('#rank').val(); },
          tree: function () { return $('#tree').val(); }
        },
        fieldNames: form_tax_field_names,
        fields: form_tax_fields,
        links: {
          name: function(row) {
            return form_tax_base_site + 'view/' + row.id;
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
            return 'Add';
          }
        }
      });

      form_tax_changed = false;
    }
  }
  
