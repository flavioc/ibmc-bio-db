<h2>Label list</h2>

{literal}
<script>
$(function () {
  start_label_list(null, null, null, true);
  $('#hide_show_labels_details').minusPlus({
    zoom: 85,
    enableImage: false,
    plusEnabled: function () {
      $('#label_show').gridShowDefault('fast');
    },
    minusEnabled: function () {
      $('#label_show').gridHideDefault('fast');
    },
    plusText: 'Show details',
    minusText: 'Hide details'
  });
});
</script>
{/literal}

{include file=label/search_form.tpl}
{include file=label/labels_grid.tpl}

<div id="hide_show_labels_details"></div>

{form_open name=form_export_labels to='label/export'}
{form_hidden name=export_name}
{form_hidden name=export_type}
{form_hidden name=export_user}
{form_submit name="submit_export" msg="Export all"}
{form_end}

{if $is_admin}
  {button name="add_label" msg="Add new" to="label/add"}
{/if}

{literal}
<style>
#form_export_labels, #form_add_label {
  display: inline;
}
</style>

<script>
$(function () {
  $('#submit_export').click(function () {
    // copy search
    $('input[name=export_name]').val($('#label_name').val());
    $('input[name=export_type]').val($('#label_type').val());
    $('input[name=export_user]').val($('#label_user').val());
    return true;
  });
});
</script>
{/literal}