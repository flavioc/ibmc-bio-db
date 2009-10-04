function start_sequence_grid(place, click, params)
{
  if(click == null) {
    click = {};
  }
  
  if(params == null) {
    params = {};
  }
  
  place
  .gridEnable()
  .grid({
    url: get_app_url() + '/sequence',
    retrieve: 'get_all',
    total: 'get_total',
    fieldNames: ['Labels', 'Name', 'Last update', 'User'],
    fields: ['labels', 'name', 'update', 'user_name'],
    tdClass: {
      update: 'centered',
      user_name: 'centered',
      labels: 'centered'
    },
    width: {
      update: '30%',
      user_name: w_user,
      labels: w_select
    },
    dataTransform: {
      labels: function (row) {
        return img_go;
      }
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
      },
      labels: function (row) {
        return get_app_url() + '/sequence/labels/' + row.id;
      }
    },
    ordering: {
      name: 'asc',
      update: 'def',
      user: 'def'
    },
    clickFun: click,
    params: params
  });
}

function activate_sequence_search(show_seqs)
{
  var form = $('#form_search');
  var name_field = $('#name', form)
  var user_field = $('#user', form);

  function when_submit()
  {
    show_seqs.gridReload();
  }

  form.validate({
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });
}