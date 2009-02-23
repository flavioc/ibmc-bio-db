<p>Coiso</p>

<a href="http://google.pt">Google</a>

{literal}
<script>
$(document).ready(function () {
  $('#ola').click(function () {
  $('#ola').hide().fadeIn('slow');
    alert("OLA!");
    self.parent.tb_remove();
    return false;
  });

  function showRequest(formData, jqForm, options) {
    var queryString = $.param(formData);

    alert("About to submit: \n\n" + queryString);

    return false;
  }

  $('#add_form').ajaxForm({
    beforeSubmit: showRequest
  });
});
</script>
{/literal}

<a id="ola" href="http://coiso.pt">Coiso</a>

{form_open to='rank/do_add' name=add_form}
<fieldset>
{form_row name=name msg='Name:'}
{form_row name=name2 msg='Coiso:'}
</fieldset>
{form_submit name=submit msg='Add'}
{form_end}

<a href="#" onclick="self.parent.tb_remove(); return false;" >OK</a>
