
{include file=header.tpl}
<h2>User list</h2>
<p>
<table class="data">
  <tr>
    <th>Image</th>
    <th>Name</th>
    <th>Complete name</th>
    <th>Email</th>
    <th>Delete</th>
  </tr>

<script>
  $(document).ready(function () {ldelim}
  $('.deletable').click(function() {ldelim}
      var id = parse_id($(this).attr('id'));
      var url = "{site}/profile/do_delete/" + id;

      $.post(url, function(data) {ldelim}
        if(is_ok(data)) {ldelim}
          var tr_id = build_tr_id(id);

          $('#' + tr_id).fadeOut("slow");
        {rdelim} else {ldelim}
          alert("Error deleting user: " + data);
        {rdelim}
      {rdelim});
      return false;
  {rdelim}).confirm();
  {rdelim});
  </script>

{foreach from=$users item=user}
  <tr id="tr_{$user.id}">
    <td><img src="{site}/image/get_id/{$user.id}/20" /></td>
    <td><a href="{site}/profile/view/{$user.id}">{$user.name}</a></td>
    <td>{$user.complete_name}</td>
    <td>{$user.email}</td>
    <td class="deletable_column">
      <a class="deletable" href="#" id="user_{$user.id}">Delete</a>
    </td>
  </tr>
{/foreach}
</table>
</p>

{include file=footer.tpl}
