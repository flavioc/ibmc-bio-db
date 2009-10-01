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
    fieldNames: ['Name', 'Last update', 'User'],
    fields: ['name', 'update', 'user_name'],
    tdClass: {
      update: 'centered',
      user_name: 'centered'
    },
    width: {
      update: '30%',
      user_name: w_user
    },
    links: {
      name: function (row) {
        return get_app_url() + '/sequence/view/' + row.id;
      },
      user_name: function (row) {
        return get_app_url() + '/profile/view/' + row.update_user_id;
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
  var changed = false;
  var form = $('#form_search');
  var name_field = $('#name', form)
  var user_field = $('#user', form);

  function changed_function ()
  {
    changed = true;
  }

  function when_submit()
  {
    if(changed) {
      show_seqs.gridReload();
    }

    changed = false;
  }

  name_field.change(changed_function);
  user_field.change(changed_function);

  form.validate({
    submitHandler: when_submit,
    errorPlacement: basicErrorPlacement
  });
}