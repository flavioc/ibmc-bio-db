{if $label.code}
{form_row type=checkbox name=generate_check msg='Generate value:'}

{literal}
<script>
$(document).ready(function () {
  var hide_dom = '#' + "{/literal}{$to_hide}{literal}";
  var hide_obj = $(hide_dom);
  var checkbox = $('#generate_check');

  checkbox.click(function (event) {
    if(checkbox.is(":checked")) {
      hide_obj.fadeOut();
    } else {
      hide_obj.fadeIn();
    }
  });

  alert(hide_dom);

});
</script>
{/literal}
{/if}
