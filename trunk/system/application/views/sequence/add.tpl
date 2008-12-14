{include file="header.tpl"}

<h2>Add sequence</h2>
<script>
{literal}
  $(document).ready(function() {
    $("#add_form").validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        },
        accession: {
          maxlength: 255
        },
        type: {
          required: true
        },
        content: {
          maxlength: 65535,
          required: true
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
{/literal}
</script>

{form_open to='sequence/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row name=accession msg='Accession number:'}
{form_row type=select data=$types name=type msg='Type:' key=name}
{form_row type=textarea name=content msg='Content:' cols=50 rows=15}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}

{include file="footer.tpl"}
