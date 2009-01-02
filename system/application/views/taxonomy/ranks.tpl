
{include file=header.tpl}
<h2>Rank list</h2>
<p>
<table class="data" id="table">
  <tr>
    <th>Name</th>
    <th class="deletable_column">Delete</th>
  </tr>
{literal}
<script>
  $(document).ready(function () {

  var base_site = '{/literal}{site}{literal}/rank/';

  function add_editable(obj) {
    obj.editable(base_site + 'edit', {
               select : true,
               submit : 'OK',
               cancel : 'cancel',
               cssclass : "editable",
               width: "200px"
           });
    }

  function add_deletable(obj) {
    obj.click(function() {
                       var id = parse_id($(this).attr('id'));
                       var url = base_site + 'delete/' + id;

                       $.post(url, function(data) {
                               if(is_ok(data)) {
                                 var tr_id = build_tr_id(id);

                                 $('#' + tr_id).fadeOut("slow");
                              } else {
                                   alert("Error deleting rank: " + data);
                              }
                              });
                       });
    obj.confirm();
  }

  add_editable($('.edit'));
  add_deletable($('.deletable'));

  function when_submit(form) {
    var new_name = $('#new_name').val();

    $.post(base_site + 'add/' + new_name, function(data) {

          if(is_ok(data)) {
             var id = parse_id(data);
             var rankedit = build_edit_id('rank', id);
             var rankdelete = build_delete_id('rank', id);
             var tr_id = build_tr_id(id);

             $('#name_error').hide(); // hide errors
             $('#new_name').val(""); // clear current inserted value

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
                                                               id: rankedit,
                                                               class: 'edit',
                                                               innerHTML: new_name
                                                               }]},
                                                  {
                                                  tagName: 'td',
                                                  class: 'deletable_column',
                                                  childNodes: [
                                                               {
                                                               tagName: 'a',
                                                               class: 'deletable',
                                                               href: '#',
                                                               id: rankdelete,
                                                               innerHTML: 'Delete'
                                                               }]
                                                  }]
                                     }]);

             $('#' + tr_id).fadeIn("slow");

             add_editable($('#' + rankedit));
             add_deletable($('#' + rankdelete));
           } else {
             // show error
             $('#name_error').show().text(data);
           }
         });
    }

    $("#form_add").validate({
        rules: {
          name: {
            required: true,
            minlength: 2,
            maxlength: 128
          }
      },
      submitHandler: when_submit,
      errorPlacement: basicErrorPlacement
    });

  });
</script>
{/literal}

{foreach from=$ranks item=rank}
  <tr id="tr_{$rank.id}">
    <td><span id="rankedit_{$rank.id}" class="edit">{$rank.name}</span></td>
    <td class="deletable_column">
      <a class="deletable" href="#" id="rankdelete_{$rank.id}">Delete</a>
    </td>
  </tr>
{/foreach}
</table>
</p>

<p>

<script>
{literal}
  $(document).ready(function() {

});
{/literal}
</script>

{form_open name=form_add}
{form_label for=name msg='New rank: '}
{form_input name=name id="new_name"}
{form_label_error for=name id=name_error}
<br />
{form_submit name=submit_add msg=Add}
{form_end}

{include file=footer.tpl}
