
var base_site = get_app_url() + '/sequence';
var seq_id = get_url_id();
var seqdata = {seq: seq_id};
var missing_loaded = false;
var addable_loaded = false;
var labels_loaded = false;
var validation_loaded = false;
var data_transform_labels = {
      subname: function (row) {
        if(row.subname == null) {
          return null;
        } else {
          return row.subname;
        }
      },
      ref_data: function (row) {
        return row.sequence_name;
      },
      taxonomy_data: function (row) {
        return row.taxonomy_name;
      },
      position_a_data: function (row) {
        return row.position_a_data + ' ' + row.position_b_data;
      },
      obj_data: function (row) {
        return row.text_data;
      },
      select: function (row) {
        return "Select";
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
        return get_app_url() + '/label_sequence/download_label/' + row.id;
      },
      select: function (row) {
        return '#row_labels_list_' + row.id;
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
        field_to_add = 'position_a_data';
        break;
      case 'obj':
        field_to_add = 'obj_data';
        break;
      case 'bool':
        field_to_add = 'bool_data';
        break;
    }
  }

  return field_to_add;
}

function reload_labels_list()
{
  labels_loaded = true;

  $('#labels_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_labels/' + seq_id,
    fieldNames: ['Name', 'Data', 'Subname', 'Type'],
    fieldGenerator: function (row) {
      return ['name', select_field(row), 'subname', 'type'];
    },
    links: link_labels,
    dataTransform: data_transform_labels,
    editables: {
      subname: {
        select: true,
        submit: 'OK',
        cancel: 'cancel',
        cssclass: 'editable',
        width: '150px'
      }
    },
    enableRemove: true,
    enableRemoveFun: function (row) {
      return row.deletable == '1';
    },
    remove: 'delete_label',
    types: {
      bool_data: 'boolean'
    }
  });
}

function load_labels_list()
{
  if(!labels_loaded) {
    reload_labels_list();
  }

  $('#labels_list').fadeIn();
}

function hide_labels_list()
{
  $('#labels_list').fadeOut();
}

var nameLink = function (row) {
  return get_app_url() + '/label/view/' + row.id;
}

var labelTypes = {
  autoadd: 'boolean',
  auto_on_creation: 'boolean',
  auto_on_modification: 'boolean',
  deletable: 'boolean',
  editable: 'boolean',
  multiple: 'boolean',
  must_exist: 'boolean'
}

var hiddenFields = ['autoadd', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'must_exist'];

function reload_missing_list()
{
  missing_loaded = true;

  $('#missing_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_missing_labels/' + seq_id,
    fieldNames: ['Select', 'Name', 'Type', 'Auto Add', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
    fields: ['select', 'name', 'type', 'autoadd', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
    links: {
      name: nameLink,
      select: function (row) {
        return '#row_addable_list_' + row.id;
      }
    },
    types: labelTypes,
    dataTransform: {
      select: function (row) {
        return 'Select';
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

function reload_addable_list()
{
  addable_loaded = true;

  $('#addable_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_addable_labels/' + seq_id,
    fieldNames: ['Add', 'Name', 'Type', 'Auto Add', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
    fields: ['add', 'name', 'type', 'autoadd', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
    links: {
      name: nameLink,
      add: function (row) {
        return '#label_' + row.id;
      }
    },
    types: labelTypes,
    dataTransform: {
      add: function (row) {
        return 'Add';
      }
    },
    clickFun: {
      add: function (row) {
        var url = get_app_url() + '/label_sequence/add_label/' + seq_id + '/' + row.id;
        tb_show('Add label', url);
      },
      type: function (row) {
      }
    },
    hiddenFields: hiddenFields
  });
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

function reload_validation_list()
{
  validation_loaded = true;

  $('#validation_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_validation_labels/' + seq_id,
    fieldNames: ['Select', 'Name', 'Data', 'Type', 'Status'],
    fieldGenerator: function (row) {
      return ['select', 'name', select_field(row), 'type', 'status'];
    },
    dataTransform: data_transform_labels,
    classFun: {
      status: function (row) {
        if(row.status == 'no validation') {
          return 'label_no_validation';
        } else if(row.status == 'invalid') {
          return 'label_invalid';
        } else if(row.status == 'valid') {
          return 'label_valid';
        }
      }
    },
    links: link_labels,
    clickFun: {
      select: function (row) {
        $('#hide_show_labels').minusPlusEnable();
        $('#labels_list').gridHighLight(row.id);
        return true;
      }
    }
  });
}

function hide_validation_list()
{
  $('#validation_box').fadeOut();
}

function load_validation_list()
{
  if(!validation_loaded) {
    reload_validation_list();
  }

  $('#validation_box').fadeIn();
}

function generate_disabled()
{
  return !checkbox_enabled('#generate_check');
}
