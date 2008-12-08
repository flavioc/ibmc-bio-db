
{if $with_username}
  {form_row name=username msg='Username:'}
{/if}
{form_row name=complete_name msg='Complete name:' size=50}
{form_row name=email msg='Email:'}

{literal}
<script>
$(document).ready(function() {
  $('#birthday')
  .datepicker({
    minDate: new Date(1920, 1-12, 1),
    maxDate: new Date(2000, 12-1, 31),
    defaultDate: new Date(1986, 12, 11),
    yearRange:'1920-2000',
    showOn: "both",
    dateFormat: "dd-mm-yy",
{/literal}
    buttonImage: "{top_dir}/images/calendar.gif"
{literal}
  });
});
{/literal}
</script>

{form_row name=birthday msg='Birthday:' type=birthday}
{form_row name=password1 msg='Password:' type=password}
{form_row name=password2 msg='Retype password:' type=password}
{form_row name=image msg="User image:" type=upload}

