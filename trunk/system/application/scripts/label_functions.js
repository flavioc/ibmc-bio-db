  
function start_label_list(params, select_fn, add_fn, link_to_seqs)
{
  if(params == null) {
    params = {};
  }
  
  if(link_to_seqs == null) {
    link_to_seqs = false;
  }

  var base_site = get_app_url() + "/label";
  var changed = false;
  var name_field = $('#label_name');
  var type_field = $('#label_type');
  var user_field = $('#label_user');
  var grid = $('#label_show');
  
  function changed_function ()
  {
    changed = true;
  }

  function when_submit()
  {
    if(changed) {
      var name_val = name_field.val();
      var type_val = type_field.val();
      var user_val = user_field.val();

      grid.gridColumnFilter('name', name_val);
      grid.gridColumnFilter('type', type_val);
      grid.gridColumnFilter('user', user_val);
      grid.gridReload();
    }

    changed = false;
  }

  // watch changes
  name_field.change(changed_function);
  type_field.change(changed_function);
  user_field.change(changed_function);

  $("#form_label_search").validate({
    rules: {
      name: {
        minlength: 0,
        maxlength: 255
      }
    },
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });

  var fieldNames = ['Name', 'Type', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple', 'Public', 'User', 'Total'];
  var fields = ['name', 'type', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'public', 'user_name', 'num_seqs'];

  if(select_fn) {
    fieldNames = $.merge(['Select'], fieldNames);
    fields = $.merge(['select'], fields);
  }

  if(add_fn) {
    fieldNames = $.merge(['Add'], fieldNames);
    fields = $.merge(['add'], fields);
  }
  
  if(link_to_seqs) {
    fieldNames = $.merge(fieldNames, ['Seqs', 'Others']);
    fields = $.merge(fields, ['seqs', 'others']);
  }

  grid.gridEnable();
  grid.grid({
    url: base_site,
    retrieve: 'get_all',
    total: 'count_total',
    fieldNames: fieldNames,
    fields: fields,
    hiddenFields: ['must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'public'],
    params: params,
    links: {
      name: function (row) {
        return base_site + '/view/' + row.id;
      },
      user_name: function(row) {
        return build_user_url(row.update_user_id);
      },
      seqs: function (row) {
        return get_app_url() + '/search?type=label&id=' + row.id.toString();
      },
      others: function (row) {
        return get_app_url() + '/search?type=notlabel&id=' + row.id.toString();
      }
    },
    dataTransform: {
      select: function (row) {
        return img_go;
      },
      add: function (row) {
        return img_add;
      },
      num_seqs: function(row) {
        return row.num_seqs;
      },
      seqs: function (row) {
        return img_lupa;
      },
      others: function (row) {
        return img_lupa;
      }
    },
    tdClass: {
      'public': 'centered',
      multiple: 'centered',
      editable: 'centered',
      deletable: 'centered',
      auto_on_modification: 'centered',
      auto_on_creation: 'centered',
      must_exist: 'centered',
      user_name: 'centered',
      select: 'centered',
      add: 'centered',
      num_seqs: 'centered',
      seqs: 'centered',
      others: 'centered'
    },
    width: {
      'public': w_boolean,
      multiple: w_boolean,
      editable: w_boolean,
      deletable: w_boolean,
      auto_on_creation: w_boolean,
      auto_on_modification: w_boolean,
      must_exist: w_boolean,
      type: w_type,
      user_name: w_user,
      add: w_add,
      num_seqs: '5%',
      seqs: '5%',
      others: '5%'
    },
    types: {
      must_exist: 'boolean',
      auto_on_creation: 'boolean',
      auto_on_modification: 'boolean',
      deletable: 'boolean',
      editable: 'boolean',
      multiple: 'boolean',
      'public': 'boolean'
    },
    ordering: {
      name: 'asc',
      type: 'def'
    },
    clickFun: {
      select: function (row) {
        select_fn(row, grid);
      },
      add: function (row) {
        add_fn(row, grid);
      },
      seqs: function (row) {
        var search_term = {label: row.name, type: row.type, oper: 'exists'};
        $.cookie('saved_search_tree', $.toJSON(search_term, true), cookie_options);
        return true;
      },
      others: function (row) {
        var search_term = {label: row.name, type: row.type, oper: 'notexists'};
        $.cookie('saved_search_tree', $.toJSON(search_term, true), cookie_options);
        return true;
      }
    }
  });
}