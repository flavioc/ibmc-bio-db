<h2>View/Edit taxonomy</h2>

<script>
{to_js var=taxonomy value=$taxonomy}

{literal}
$(document).ready(function() {
  var base_site = get_app_url() + "/taxonomy/";
  var tax_id = taxonomy.id;
  var taxdata = {tax: tax_id};

  $('#taxname').editable(base_site + 'edit_name', {
    select: true,
    submit: 'OK',
    cancel: 'cancel',
    width: "150px",
    style: "inherit",
    submitdata: taxdata
  });

  $('#taxrank').editable(base_site + 'edit_rank', {
    data: {/literal}{encode_json_data data=$ranks blank=yes key=rank_id value=rank_name}{literal},
    type: "select",
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    submitdata: taxdata
  });

  $('#taxtree').editable(base_site + 'edit_tree', {
    data: {/literal}{encode_json_data data=$trees blank=yes}{literal},
    type: 'select',
    submit: 'OK',
    cancel: 'cancel',
    style: 'inherit',
    submitdata: taxdata
  });

  $('#set_parent').click(function () {
    tb_show('Select parent',
      base_site + 'select_parent/' + taxonomy.id);
  });

});
{/literal}
</script>
<div class="data_show">
  <p><span class="desc">Name: </span><span id="taxname" class="writeable">{$taxonomy.name}</span></p>
  <p><span class="desc">Rank: </span><span id="taxrank" class="writeable">{if $taxonomy.rank_name}{$taxonomy.rank_name}{else}---{/if}</span></p>
  <p><span class="desc">Tree: </span><span id="taxtree" class="writeable">{if $taxonomy.tree_name}{$taxonomy.tree_name}{else}---{/if}</span></p>
  <p><span class="desc"><span class="clickable"><a href="#" id="set_parent">Parent:</a></span> </span>
  {if $parent}
    <a href="{site}/taxonomy/view/{$parent.id}">{$parent.name}</a>
  {else}
    ---
  {/if}
  </p>
{include file="history/form_view.tpl" data=$taxonomy}
</div>

{form_open name=form_delete to="taxonomy/delete_redirect"}
{form_hidden name=id value=$taxonomy.id}
{form_submit name=submit_delete msg=Delete}
{form_end}

<hr />

<h3>Other names</h3>
<p>
<div id="other_names">
</div>
<br />

<script>
{literal}
  $(document).ready(function() {
    var tax_id = taxonomy.id;
    var base_site = get_app_url() +"/taxonomy_name/";

    $('#other_names')
    .gridEnable({paginate: false})
    .grid({
      url: get_app_url() + '/taxonomy_name',
      retrieve: 'list_all/' + tax_id,
      fieldNames: ['Name', 'Type'],
      fields: ['name', 'type_name'],
      enableRemove: true,
      dataTransform: {
        '$delete': function (row) {
          return img_del;
        }
      },
      tdClass: {
        '$delete' : 'centered',
        type_name: 'centered'
      },
      width: {
        '$delete': w_del,
        type_name: w_type
      },
      editables: {
        name: {
          select : true,
          submit : 'OK',
          cancel : 'cancel',
          width: "400px"
        },
        type_name: { 
          data   : {/literal}{encode_json_data data=$types}{literal},
          type   : "select",
          submit : "OK",
          cancel : 'cancel',
          style  : "inherit"
        }
      }
    });

    function when_submit() {
      var new_name = $('#new_name').val();
      var new_type = $('#new_type').val();

      $.post(base_site + 'add/' + tax_id + '/' + new_name + '/' + new_type,
        function(data) {
          var obj = $.evalJSON(data);

          $('#other_names').gridAdd(obj);
      });
    }

    $("#form_add").validate({
      rules: {
        new_name: {
          required: true,
          minlength: 2,
          maxlength: 512
        },
        new_type: {
          required: true
        }
      },
      submitHandler: when_submit,
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open name=form_add}
{form_row name=new_name msg='New name:'}
{form_row type=select data=$types name=new_type msg='Type:'}
{form_submit name=submit_add msg=Add}
{form_end}

</p>
<br />

<hr />

<h3>Children</h3>
<p>
<div id="child_taxonomies">
</div>

{form_open name=form_add_child to="taxonomy/add" method="get"}
{form_hidden name="parent_id" value=$taxonomy.id}
{form_submit name=submit_add_child msg="Add child"}
{form_end}

{literal}
<script>
$(document).ready(function () {
  var div = $('#child_taxonomies');
  var base_site = get_app_url() + "/taxonomy";

  div.gridEnable();

  div.grid({
    url: base_site,
    total: 'total_taxonomy_childs/' + taxonomy.id,
    retrieve: 'taxonomy_childs/' + taxonomy.id,
    fieldNames: ['Name', 'Rank', 'Tree'],
    fields: ['name', 'rank_name', 'tree_name'],
    tdClass: {
      tree_name: 'centered',
      rank_name: 'centered'
    },
    width: {
      rank_name: w_rank,
      tree_name: w_tree
    },
    links: {
        name: function(row) {
          return base_site + '/view/' + row.id;
        },
        rank_name: function (row) {
          return get_app_url() + '/rank/view/' + row.rank_id;
        }
    }
  });

});
</script>
{/literal}
