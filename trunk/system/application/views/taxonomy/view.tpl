<h2>View/Edit taxonomy</h2>


<script>
{literal}
$(document).ready(function() {
  var base_site = "{/literal}{site}{literal}/taxonomy/";
  var tax_id = "{/literal}{$taxonomy.id}{literal}";
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

});
{/literal}
</script>
<div class="data_show">
  <p><span class="desc">Name: </span><span id="taxname" class="writeable">{$taxonomy.name}</span></p>
  <p><span class="desc">Rank: </span><span id="taxrank" class="writeable">{if $taxonomy.rank_name}{$taxonomy.rank_name}{else}---{/if}</span></p>
  <p><span class="desc">Tree: </span><span id="taxtree" class="writeable">{if $taxonomy.tree_name}{$taxonomy.tree_name}{else}---{/if}</span></p>
  <p><span class="desc"><a href="{site}/taxonomy/browse_parent/{$taxonomy.id}">Parent:</a> </span>
  {if $parent}
    <a href="{site}/taxonomy/view/{$parent.id}">{$parent.name}</a>
  {else}
    ---
  {/if}
  </p>
{include file="history/form_view.tpl" data=$taxonomy}
</div>

{assign var=tax_id value=$taxonomy.id}
{form_open name=form_delete to="taxonomy/delete_redirect/$tax_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

<h3>Other names</h3>
<p>
<div id="other_names">
</div>
<br />

<script>
{literal}
  $(document).ready(function() {
    var tax_id = {/literal}{$taxonomy.id}{literal};
    var base_site = get_app_url() +"/taxonomy_name/";

    $('#other_names')
    .gridEnable({paginate: false})
    .grid({
      url: get_app_url() + '/taxonomy_name',
      retrieve: 'list_all/' + {/literal}{$taxonomy.id}{literal},
      fieldNames: ['Name', 'Type'],
      fields: ['name', 'type_name'],
      enableRemove: true,
      editables: {
        name: {
          select : true,
          submit : 'OK',
          cancel : 'cancel',
          width: "200px"
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
{button name="browse_tax" msg="List taxonomies" to="taxonomy/browse"}
