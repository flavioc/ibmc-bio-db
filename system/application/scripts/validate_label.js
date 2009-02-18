
function validate_label_form (dom)
{
  $(document).ready(function() {
    var auto_on_creation = $("#auto_on_creation");
    var auto_on_modification = $("#auto_on_modification");

    function code_is_required() {
      var checked_creation = auto_on_creation.is(":checked");
      var checked_modification = auto_on_modification.is(":checked");

      return checked_creation || checked_modification;
    }

    function autoadd_is_required() {
      var checked_creation = auto_on_creation.is(":checked");

      return checked_creation;
    }

    $(dom).validate({
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        },
        type: {
          required: true
        },
        autoadd: {
          required: autoadd_is_required
        },
        code: {
          required: code_is_required
        }
      },
      errorPlacement: basicErrorPlacement
    });
  });
}
