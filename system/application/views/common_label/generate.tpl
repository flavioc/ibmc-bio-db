{if $label.code}
{form_row type=checkbox name=generate_check msg='Generate value:'}

{literal}
<script>
$(document).ready(function () {
  var hide_dom = "{/literal}{$to_hide}{literal}";
  var hide_obj = $(hide_dom);
  var hide_node = hide_obj[0];
  var checkbox = $('#generate_check');

  checkbox.click(function (event) {
    if(checkbox.is(":checked")) {
      hide_obj.hide();
    } else {
      hide_obj.show();
    }
  });

});
</script>
{/literal}
{/if}
