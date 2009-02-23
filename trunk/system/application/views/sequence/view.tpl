<h2>View/Edit sequence</h2>

{assign var=seq_id value=$sequence.id}

<script>
{literal}
$(document).ready(function() {
  var base_site = get_app_url() + '/sequence';
  var seq_id = "{/literal}{$seq_id}{literal}";
  var seqdata = {seq: seq_id};
  var missing_loaded = false;
  var addable_loaded = false;

  $('#seqname').editable(base_site + '/edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: seqdata
  });

  $('#seqaccession').editable(base_site + '/edit_accession', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: seqdata
  });

  $('#seqcontent').editable(base_site + '/edit_content', {
    select: true,
    type: 'textarea',
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    cols: 50,
    rows: 5,
    submitdata: seqdata,
    finishHook: load_labels_list
  });

  function load_labels_list()
  {
    $('#labels_list')
    .grid({
      url: base_site,
      retrieve: 'get_labels/' + seq_id,
      fieldNames: ['Data', 'Name', 'Subname', 'Type'],
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
          }
        }

        return [field_to_add, 'name', 'subname', 'type'];
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
          return get_app_url() + '/sequence/download_label/' + row.id;
        }
      },
      dataTransform: {
        subname: function (row) {
          if(row.subname == null) {
            return '---';
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
      remove: 'delete_label'
    });
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
      url: get_app_url() + '/sequence',
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
      url: get_app_url() + '/sequence',
      retrieve: 'get_addable_labels/' + seq_id,
      fieldNames: ['Add', 'Name', 'Type', 'Auto Add', 'Must Exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple'],
      fields: ['add', 'name', 'type', 'autoadd', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple'],
      links: {
        name: nameLink,
        add: function (row) {
          return '#';
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
          tb_show("Coiso", get_app_url() + '/help/settings');
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

  $('#labels_list').gridEnable({paginate: false});
  $('#missing_list').gridEnable({paginate: false});
  $('#addable_list').gridEnable({paginate: false});

  hide_addable_list();
  hide_missing_list();
  load_labels_list();

  $('#hide_show_missing').minusPlus({
    enabled: false,
    plusEnabled: load_missing_list,
    minusEnabled: hide_missing_list
  });

  $('#hide_show_missing_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#missing_list').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#missing_list').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });

  $('#hide_show_addable_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#addable_list').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#addable_list').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });

  $('#hide_show_addable').minusPlus({
    plusEnabled: load_addable_list,
    minusEnabled: hide_addable_list
  });

});
{/literal}
</script>

<div class="data_show">
  <p><span class="desc">Name: </span><span id="seqname">{$sequence.name}</span></p>
  <p><span class="desc">Type: </span><span id="seqtype">{$sequence.type}</span></p>
  <p><span class="desc">Accession Number: </span><span id="seqaccession">{$sequence.accession}</span></p>
  <p><span class="desc">Content: </span><span id="seqcontent">{$sequence.content}</span></p>
</div>

{form_open name=form_delete to="sequence/delete/$seq_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

<p>
<h3>Associated labels</h3>
<div id="labels_list">
</div>
</p>

<hr />

<p>
<h3>Missing labels</h3><div id="hide_show_missing"></div>
<div id="missing_box">
  <div id="missing_list">
  </div>
  <br />
  <div id="hide_show_missing_details">
  </div>
</div>
</p>

<hr />

<p>
<h3>Addable labels</h3><div id="hide_show_addable"></div>
<div id="addable_box">
  <div id="addable_list">
  </div>
  <br />
  <div id="hide_show_addable_details">
  </div>
</div>
</p>

<hr />

<p>
<br />
<a href="{site}/sequence/browse">Sequence List</a>
</p>


