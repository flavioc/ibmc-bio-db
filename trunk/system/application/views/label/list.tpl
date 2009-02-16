<h2>Label list</h2>

{literal}
<script>
$(document).ready(function () {
  $('#label_show')
  .gridEnable({paginate: false})
  .grid({
    url: get_app_url() + '/label',
    retrieve: 'get_all',
    fieldNames: ['Name', 'Type', 'Auto Add', 'Must Exist', 'Generate on creation', 'Generate on modification'],
    fields: ['name', 'type', 'autoadd', 'must_exist', 'auto_on_creation', 'auto_on_modification'],
    countRemove: 'total_sequences',
    what: 'label',
    removeAssociated: 'sequences',
    enableRemove: true,
    links: {
      name: function (row) {
        return get_app_url() + '/label/view/' + row.id;
      }
    },
    types: {
      autoadd: 'boolean',
      must_exist: 'boolean',
      auto_on_creation: 'boolean',
      auto_on_modification: 'boolean'
    },
    enableRemoveFun: function (row) {
      return row.default == '0';
    }
  });
});
</script>
{/literal}

<p>
<div id="label_show">
</div>
</p>

