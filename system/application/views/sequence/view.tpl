<h2>View sequence</h2>

{assign var=seq_id value=$sequence.id}

<script>
{literal}
$(document).ready(function() {
  var base_site = get_app_url() + '/sequence';
  var seq_id = "{/literal}{$seq_id}{literal}";
  var seqdata = {seq: seq_id};

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
    submitdata: seqdata
  });

  $('#labels_list')
  .gridEnable({paginate: false})
  .grid({
    url: base_site,
    retrieve: 'get_labels/' + seq_id,
    fieldNames: ['Name', 'Subname', 'Type', 'Data'],
    fieldGenerator: function (row) {
      var fields = ['name', 'subname', 'type'];

      switch(row.type) {
        case 'integer':
          fields.push('int_data');
          break;
        case 'text':
          fields.push('text_data');
          break;
        case 'url':
          fields.push('url_data');
          break;
        case 'ref':
          fields.push('ref_data');
          break;
        case 'tax':
          fields.push('taxonomy_data');
          break;
        case 'position':
          fields.push('position_a_data');
          break;
        default:
          fields.push('int_data');
          break;
      }

      return fields;
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
