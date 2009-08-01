<h2>Import labels report</h2>

<p>The next table shows the import results:</p>

{literal}<script>
$(function () {
  var grid = $('#show_labels');
  
  $('#show_labels')
  .gridEnable()
  .grid({
    method: 'local',
    local_data: {/literal}{encode_json value=$labels}{literal},
    fieldNames: ['Success', 'Mode', 'Name', 'Type', 'Must exist', 'Creation', 'Modification', 'Deletable', 'Editable', 'Multiple', 'Default', 'Public'],
    fields: ['success', 'mode', 'name', 'type', 'must_exist', 'auto_on_creation', 'auto_on_modification', 'deletable', 'editable', 'multiple', 'default', 'public'],
    tdClass: {
      type: 'centered',
      success: 'centered',
      mode: 'centered',
      multiple: 'centered',
      editable: 'centered',
      deletable: 'centered',
      auto_on_modification: 'centered',
      auto_on_creation: 'centered',
      must_exist: 'centered',
      default: 'centered',
      public: 'centered'
    },
    dataTransform: {
      success: function (row) {
        if(row.ret == true) {
          // edit
          return true;
        }
        
        if(row.ret == false) {
          return false;
        }
        
        return int > 0;
      },
      mode: function (row) {
        if(row.mode == 'edit') {
          return 'Edit';
        } else {
          return 'Add';
        }
      }
    },
    width: {
      success: w_boolean,
      mode: w_boolean,
      multiple: w_boolean,
      editable: w_boolean,
      deletable: w_boolean,
      auto_on_creation: w_boolean,
      auto_on_modification: w_boolean,
      must_exist: w_boolean,
      type: w_type
    },
    types: {
      success: 'boolean',
      must_exist: 'boolean',
      auto_on_creation: 'boolean',
      auto_on_modification: 'boolean',
      deletable: 'boolean',
      editable: 'boolean',
      multiple: 'boolean',
      default: 'boolean',
      public: 'boolean'
    },
    links: {
      name: function (row) {
        return get_app_url() + '/label/view/' + row.id;
      }
    }
  });
});
</script>{/literal}
    
<div id="show_labels">
</div>