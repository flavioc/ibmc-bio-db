<h2>View taxonomy</h2>

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
    data: {/literal}{encode_json_data data=$ranks}{literal},
    type: "select",
    submit: "OK",
    cancel: "cancel",
    style: "inherit",
    submitdata: taxdata
  });

});
{/literal}
</script>
<div class="data_show">
  <p><span class="desc">Name: </span><span id="taxname">{$taxonomy.name}</span></p>
  <p><span class="desc">Rank: </span><span id="taxrank">{$taxonomy.rank_name}</span></p>
  <p><span class="desc"><a href="{site}/taxonomy/browse_parent/{$taxonomy.id}">Parent:</a> </span>
  {if $parent}
    <a href="{site}/taxonomy/view/{$parent.id}">{$parent.name}</a>
  {else}
    -
  {/if}
  </p>
</div>

{assign var=tax_id value=$taxonomy.id}
{form_open name=form_delete to="taxonomy/delete_redirect/$tax_id"}
{form_submit name=submit_delete msg=Delete}
{form_end}

<h3>Other names</h3>
<p>
<table class="data" id="table">
  <tr>
    <th>Name</th>
    <th>Type</th>
    <th class="deletable_column">Delete</th>
  </tr>
  {foreach from=$names item=name}
  <tr id="tr_{$name.id}">
    <td><span id="nameedit_{$name.id}" class="edit">{$name.name}</span></td>
    <td><span id="typeedit_{$name.id}" class="edit_select">{$name.type_name}</span></td>
    <td class="deletable_column">
      <a class="deletable" href="#" id="namedelete_{$name.id}">Delete</a>
    </td>
  </tr>
{/foreach}
</table>
<br />

<script>
{literal}
  $(document).ready(function() {
    var tax_id = {/literal}{$taxonomy.id}{literal};
    var base_site = "{/literal}{site}{literal}/taxonomy_name/";

    function add_deletable(obj) {
      obj.click(function() {
        var id = parse_id($(this).attr('id'));
        var url = base_site + 'delete/' + id;

        $.post(url, function(data) {
          if(is_ok(data)) {
            var tr_id = build_tr_id(id);

            $('#' + tr_id).fadeOut("slow");
          } else {
            alert("Error deleting name: " + data);
          }
        });
      }).confirm();
    }

    add_deletable($('.deletable'));

    function add_editable(obj) {
      obj.editable(base_site + 'edit_name', {
               select : true,
               submit : 'OK',
               cancel : 'cancel',
               width: "200px"
           });
    }

    add_editable($('.edit'));

    function add_editable_select(obj) {
      obj.editable(base_site + 'edit_type', { 
        data   : {/literal}{encode_json_data data=$types}{literal},
        type   : "select",
        submit : "OK",
        cancel : 'cancel',
        style  : "inherit"
      });
    }

    add_editable_select($('.edit_select'));

    function when_submit() {
      var new_name = $('#new_name').val();
      var new_type = $('#new_type').val();

      $.post(base_site + 'add/' + tax_id + '/' + new_name + '/' + new_type, function(data) {
        //alert(data);

        if(is_ok(data)) {
          var id = parse_id(data);
          var type = $('#new_type')[0];
          var new_type_name = type.options[type.selectedIndex].text;

          $('#new_name').val('');
          $('#new_name_error').hide(); // hide errors

          var nameedit = build_edit_id('name', id);
          var typeedit = build_edit_id('type', id);
          var namedelete = build_delete_id('name', id);
          var tr_id = build_tr_id(id);

           $('#table').appendDom([{
                                   tagName: 'tr',
                                   id: tr_id,
                                   style: "display: none;",
                                   childNodes: [
                                                {
                                                tagName: 'td',
                                                childNodes: [
                                                             {
                                                             tagName: 'span',
                                                             id: nameedit,
                                                             class: 'edit',
                                                             innerHTML: new_name
                                                             }]},
                                                {
                                                tagName: 'td',
                                                childNodes: [
                                                  {
                                                    tagName: 'span',
                                                    id: typeedit,
                                                    class: 'edit_select',
                                                    innerHTML: new_type_name
                                                  }
                                                  ]
                                                },
                                                {
                                                tagName: 'td',
                                                class: 'deletable_column',
                                                childNodes: [
                                                             {
                                                             tagName: 'a',
                                                             class: 'deletable',
                                                             href: '#',
                                                             id: namedelete,
                                                             innerHTML: 'Delete'
                                                             }]
                                                }]
                                   }]);

           $('#' + tr_id).fadeIn("slow");
           add_editable($('#' + nameedit));
           add_editable_select($('#' + typeedit));
           add_deletable($('#' + namedelete));

        } else {
          $('#new_name_error').show().text(data);
        }
        
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
