
var base_site = get_app_url() + '/sequence';
var seq_id = get_url_id();
var seqdata = {seq: seq_id};
var missing_loaded = false;
var addable_loaded = false;
var labels_loaded = false;

function reload_labels_list()
{
  labels_loaded = true;

  $('#labels_list')
  .grid({
    url: get_app_url() + '/label_sequence',
    retrieve: 'get_labels/' + seq_id,
    fieldNames: ['Name', 'Data', 'Subname', 'Type'],
    fieldGenerator: function (row) {
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

      return ['name', field_to_add, 'subname', 'type'];
    },
    links: {
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
      }
    },
    dataTransform: {
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
      }
    },
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

  $('#labels_list').fadeIn('slow');
}

function hide_labels_list()
{
  $('#labels_list').fadeOut('slow');
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
    fieldNames: ['Name', 'Type', 'Auto Add', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
    fields: ['name', 'type', 'autoadd', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
    links: {
      name: nameLink
    },
    types: labelTypes,
    hiddenFields: hiddenFields
  });
}

function load_missing_list()
{
  if(!missing_loaded) {
    reload_missing_list();
  }

  $('#missing_box').fadeIn('slow');
}

function hide_missing_list()
{
  $('#missing_box').fadeOut('slow');
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
  $('#addable_box').fadeOut('slow');
}

function load_addable_list()
{
  if(!addable_loaded) {
    reload_addable_list();
  }

  $('#addable_box').fadeIn('slow');
}

function generate_disabled()
{
  return !checkbox_enabled('#generate_check');
}
