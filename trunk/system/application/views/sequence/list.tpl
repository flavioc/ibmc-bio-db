<h2>Sequence list</h2>

{literal}
<script>
$(function () {
  var name_field = $('#name');
  var user_field = $('#user');
  var show_seqs = $('#show_sequences');
  
  activate_sequence_search(show_seqs);
  
  start_sequence_grid(show_seqs, {}, {
      name: function () { return $('#name').val(); },
      user: function () { return $('#user').val(); }
  });
  
  $('#submit_export').click(function () {
    $('input[name=export_name]').val(name_field.val());
    $('input[name=export_user]').val(user_field.val());
    
    return true;
  });
});
</script>
{/literal}

{include file=sequence/form_search.tpl}

<div id="show_sequences"></div>

{form_open to='export/export_all' name=export_form}
{form_hidden name=export_name}
{form_hidden name=export_user}
{include file=export/types.tpl}
{form_submit name=submit_export msg='Export all'}
{form_end}

{form_open to='search?type=list' name=form_search_seqs}
{form_submit msg='Search'}
{form_end}

{if $logged_in}
{button name="add_seq" msg="Add new" to="sequence/add"}
{/if}

{literal}
<style>
#form_add_seq, #export_form, #form_search_seqs {
  display: inline;
}
</style>

<script>
$(function () {
  $('#form_search_seqs').submit(function () {
    var name = $('#name').val();
    var user = $('#user').children('[@selected]').text();
    var operand_list = [];
    
    if(name != '') {
      operand_list.push({label: 'name', type: 'text', oper: 'regexp', value: name});
    }
    
    if(user != '') {
      operand_list.push({label: 'update_user', type: 'text', oper: 'regexp', value: user});
    }
    
    var obj = null;
    if(operand_list.length != 0)
      obj = {oper: 'and', operands: operand_list};
    else
      obj = {label: 'name', type: 'text', oper: 'exists'};
      
    $.cookie('saved_search_tree', $.toJSON(obj, true), cookie_options);
    
    return true;
  });
});
</script>
{/literal}
