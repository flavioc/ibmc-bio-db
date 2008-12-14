{include file=header.tpl}

<h2>Label list</h2>
<p>
<table class="data">
  <tr>
    <th>Name</th>
    <th>Type</th>
    <th>Auto Add</th>
    <th>Delete</th>
  </tr>

{literal}
<script>
  $(document).ready(function () {
    var base_site = "{/literal}{site}{literal}/label/";

    $('.deletable').click(function() {
      var id = parse_id($(this).attr('id'));
      var url = base_site + "delete/" + id;

      $.post(url, function(data) {
        if(is_ok(data)) {
          var tr_id = build_tr_id(id);
          $('#' + tr_id).fadeOut("slow");
        } else {
          alert("Error deleting label: " + data);
        }
      });
      return false;
    }).confirm();
  });
</script>
{/literal}

{foreach from=$labels item=label}
  <tr id="tr_{$label.id}">
    <td><a href="{site}/label/view/{$label.id}">{$label.name}</a></td>
    <td>{$label.type}</td>
    <td>{if $label.autoadd}
          Yes
        {else}
          No
        {/if}
    </td>
    <td class="deletable_column">
      {if $label.default}
        ---
      {else}
        <a class="deletable" href="#" id="label_{$label.id}">Delete</a>
      {/if}
    </td>
  </tr>
{/foreach}
</table>
</p>

{include file=footer.tpl}
