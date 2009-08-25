
var base_site = get_app_url() + '/sequence';
var label_site = get_app_url() + '/label_sequence';
var missing_loaded = false;
var addable_loaded = false;
var bad_multiple_loaded = false;
var logged_in = get_logged_in();
var data_transform_labels = {
      ref_data: function (row) {
        return row.sequence_name;
      },
      taxonomy_data: function (row) {
        return row.taxonomy_name;
      },
      position_start: function (row) {
        return row.position_start + ' ' + row.position_length;
      },
      obj_data: function (row) {
        return row.text_data;
      },
      date_data: function (row) {
        return row.date_data;
      },
      select: function (row) {
        return "Select";
      },
      edit: function (row) {
        return img_edit;
      },
      '$delete': function (row) {
        return img_del;
      },
      select: function (row) {
        return img_go;
      }
  };
var link_labels = {
      name: function (row) {
        return get_app_url() + '/label/view/' + row.label_id;
      },
      url_data: function (row) {
        return row.url_data;
      },
      ref_data: function (row) {
        return get_app_url() + '/sequence/view/' + row.ref_data;
      },
      taxonomy_data: function (row) {
        return get_app_url() + '/taxonomy/view/' + row.taxonomy_data;
      },
      obj_data: function (row) {
        return label_site + '/download_label/' + row.id;
      },
      select: function (row) {
        return '#row_labels_list_' + row.id;
      },
      edit: function (row) {
        return '#row_labels_list_' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      }
    };

function select_field(row)
{
  var field_to_add = 'data';

  if(row) {
    switch(row.type) {
      case 'integer':
        field_to_add = 'int_data';
        break;
      case 'text':
        field_to_add = 'text_data';
        break;
      case 'url':
        field_to_add = 'url_data';
        break;
      case 'ref':
        field_to_add = 'ref_data';
        break;
      case 'tax':
        field_to_add = 'taxonomy_data';
        break;
      case 'position':
        field_to_add = 'position_start';
        break;
      case 'obj':
        field_to_add = 'obj_data';
        break;
      case 'date':
        field_to_add = 'date_data';
        break;
      case 'bool':
        field_to_add = 'bool_data';
        break;
    }
  }

  return field_to_add;
}

function setup_labels_list()
{
  var options = {
    url: label_site,
    retrieve: 'get_labels/' + seq_id,
    tdClass: {
      '$delete': 'centered',
      update: 'centered',
      edit: 'centered',
      type: 'centered'
    },
    width: {
      update: w_update,
      '$delete': w_del,
      type: w_type,
      edit: w_edit,
      name: w_label_name,
      user_name: w_user
    },
    fieldNames: ['Name', 'Data', 'Type', 'Update', 'User'],
    fieldGenerator: function (row) {
      var base = ['name', select_field(row), 'type', 'update', 'user_name'];

      if(logged_in) {
        base.push('edit');
      }

      return base;
    },
    hiddenFields: ['user_name', 'update'],
    links: link_labels,
    dataTransform: data_transform_labels,
    enableRemove: logged_in,
    enableRemoveFun: function (row) {
      return sql_true(row.deletable);
    },
    remove: 'delete_label',
    types: {
      bool_data: 'boolean'
    },
    deleteFun: function (id) {
      if(bad_multiple_loaded) {
        $('#bad_multiple_list').gridDeleteRow(id);
      }
      reload_addable_list();
    },
    clickFun: {
      edit: function (row) {
        var url = get_app_url() + '/edit_labels/edit_dialog/' + row.id;
        tb_show('Edit label', url);
      }
    }
  };

  if(logged_in) {
    options.fieldNames.push('Edit');
  }

  $('#labels_list').grid(options);
}

function reload_labels_list()
{
  $('#labels_list').gridReload();
}

function load_labels_list()
{
  setup_labels_list();

  $('#labels_box').fadeIn();
}

function hide_labels_list()
{
  $('#labels_box').fadeOut();
}

var nameLink = function (row) {
  return get_app_url() + '/label/view/' + row.id;
}

var labelTypes = {
  auto_on_creation: 'boolean',
  auto_on_modification: 'boolean',
  deletable: 'boolean',
  editable: 'boolean',
  multiple: 'boolean',
  must_exist: 'boolean'
}

var hiddenFields = ['auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'must_exist'];

function setup_missing_list()
{
  missing_loaded = true;

  $('#missing_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_missing_labels/' + seq_id,
    fieldNames: ['Select', 'Name', 'Type', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
    fields: ['select', 'name', 'type', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
    links: {
      name: nameLink,
      select: function (row) {
        return '#row_addable_list_' + row.id;
      }
    },
    tdClass: {
      multiple: 'centered',
      editable: 'centered',
      deletable: 'centered',
      auto_on_modification: 'centered',
      auto_on_creation: 'centered',
      type: 'centered',
      select: 'centered'
    },
    width: {
      multiple: w_boolean,
      editable: w_boolean,
      deletable: w_boolean,
      auto_on_modification: w_boolean,
      auto_on_creation: w_boolean,
      type: w_type,
      select: w_select
    },
    types: labelTypes,
    dataTransform: {
      select: function (row) {
        return img_go;
      }
    },
    hiddenFields: hiddenFields,
    clickFun: {
      select: function (row) {
        $('#hide_show_addable').minusPlusEnable();
        $('#addable_list').gridHighLight(row.id);
        return true;
      }
    }
  });
}

function reload_missing_list()
{
  if(!missing_loaded) {
    setup_missing_list();
  } else {
    $('#missing_list').gridReload();
  }
}

function ensure_missing_list_loaded()
{
  if(!missing_loaded) {
    reload_missing_list();
  }
}

function load_missing_list()
{
  ensure_missing_list_loaded();
  $('#missing_box').fadeIn();
}

function hide_missing_list()
{
  $('#missing_box').fadeOut();
}

function setup_addable_list()
{
  $('#addable_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_addable_labels/' + seq_id,
    fieldNames: ['Add', 'Name', 'Type', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
    fields: ['add', 'name', 'type', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
    links: {
      name: nameLink,
      add: function (row) {
        return '#label_' + row.id;
      }
    },
    tdClass: {
      auto_on_creation: 'centered',
      auto_on_modification: 'centered',
      deletable: 'centered',
      editable: 'centered',
      multiple: 'centered',
      must_exist: 'centered',
      type: 'centered',
      add: 'centered'
    },
    width: {
      add: w_add,
      type: w_type,
      auto_on_creation: w_boolean,
      auto_on_modification: w_boolean,
      deletable: w_boolean,
      editable: w_boolean,
      multiple: w_boolean,
      must_exist: w_boolean
    },
    types: labelTypes,
    dataTransform: {
      add: function (row) {
        return img_add;
      }
    },
    clickFun: {
      add: function (row) {
        var url = get_app_url() + '/add_labels/add_dialog/' + seq_id + '/' + row.id;
        tb_show('Add label', url);
      },
      type: function (row) {
      }
    },
    hiddenFields: hiddenFields
  });

  addable_loaded = true;
}

function reload_addable_list()
{
  if(addable_loaded) {
    $('#addable_list').gridReload();
  } else {
    setup_addable_list();
  }
}

function hide_addable_list()
{
  $('#addable_box').fadeOut();
}

function ensure_addable_list_loaded()
{
  if(!addable_loaded) {
    reload_addable_list();
  }
}

function load_addable_list()
{
  ensure_addable_list_loaded();
  $('#addable_box').fadeIn();
}

function hide_bad_multiple_list()
{
  $('#bad_multiple_box').fadeOut();
}

function load_bad_multiple_list()
{
  if(!bad_multiple_loaded) {
    reload_bad_multiple_list();
  }

  $('#bad_multiple_box').fadeIn();
}

function reload_bad_multiple_list()
{
  bad_multiple_loaded = true;

  $('#bad_multiple_list')
  .grid({
    url: label_site,
    retrieve: 'get_bad_multiple_labels/' + seq_id,
    fieldNames: ['Select', 'Name', 'Data', 'Type'],
    fieldGenerator: function (row) {
      return ['select', 'name', select_field(row), 'type'];
    },
    dataTransform: data_transform_labels,
    links: {
      name: nameLink,
      select: function (row) {
        return '#row_labels_list_' + row.id;
      }
    },
    clickFun: {
      select: function (row) {
        $('#hide_show_labels').minusPlusEnable();
        $('#labels_list').gridHighLight(row.id);
        return true;
      }
    }
  });
}

function after_edit_label(form, responseText, statusText) {
  var resp = $.evalJSON(responseText);

  if(resp == true) {
    reload_labels_list();
    tb_remove();
  } else {
    if(resp == false) {
      responseText = "Error editing label: invalid label data?";
    } else {
      responseText = resp;
    }
    
    show_error_label(form, responseText);
  }
}

$.fn.ajaxFormEdit = function () {
  var form = this;
  
  return form.ajaxForm({
    success: function (txt, status) { after_edit_label(form, txt, status);},
    beforeSubmit: function (data, form) {
      return $(form).valid();
    }
  });
};

function after_add_label(form, responseText, statusText) {
  var resp = $.evalJSON(responseText);

  if(resp == true) {
    if(label.multiple == 0) {
      reload_addable_list();
    }

    if(label.must_exist == 1) {
      reload_missing_list();
    }

    reload_labels_list();
    
    tb_remove();
  } else {
    if(resp == false) {
      responseText = "Error inserting label: invalid label data?";
    } else {
      responseText = resp;
    }
    
    show_error_label(form, responseText);
  }
}

function show_error_label(form, text)
{
  $('#label_error').show();
  $('input[type=text]', form).focus();
  $('#label_error_txt').text(text);
}

$.fn.ajaxFormAdd = function () {
  var form = this;
  
  return form.ajaxForm({
    success: function (txt, status) { after_add_label(form, txt, status);},
    beforeSubmit: function (data, form) {
      return $(form).valid();
    }
  });
};