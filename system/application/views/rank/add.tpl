<h2>Add rank</h2>
<script>
{literal}
  $(document).ready(function() {
    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 128
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='rank/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$ranks name=parent_id msg='Parent:' blank=yes start=0 key=rank_id value=rank_name}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}
