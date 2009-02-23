<h2>Add taxonomy</h2>
<script>
{literal}
  $(document).ready(function() {
    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 512
        },
        rank: {
          required: true
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='taxonomy/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row type=select data=$ranks name=rank msg='Rank:' blank=yes start=0 key=rank_id value=rank_name}
{form_row type=select data=$trees name=tree msg='Tree:' blank=yes start=0}
{if $parent_name}
{form_hidden name=parent_id value=$parent_id}
  <p><span class="desc">Parent: </span><strong>{$parent_name}</strong></p>
{/if}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}
