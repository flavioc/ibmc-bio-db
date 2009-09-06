<h2>User list</h2>

{literal}
<script>

$(document).ready(function () {
  $('#user_list')
  .gridEnable({paginate: false})
  .grid({
    url: get_app_url() + '/profile',
    retrieve: 'get_all',
    fieldNames: ['Image', 'Name', 'Complete name', 'Email', 'Last access'],
    fields: ['image', 'name', 'complete_name', 'email', 'last_access'],
    width: {
      name: w_user,
      email: w_email,
      image: w_image,
      last_access: w_update
    },
    dataTransform: {
      image: function (row) {
        return '<img src="' + get_app_url() + '/image/get_id/' + row.id + '/20" />';
      }
    },
    links: {
      name: function (row) {
        return get_app_url() + '/profile/view/' + row.id;
      }
    }
  });
});
</script>
{/literal}

<p>
<div id="user_list">
</div>
</p>
