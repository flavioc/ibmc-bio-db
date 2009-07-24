function generate_disabled()
{
  return !checkbox_enabled('#generate_check');
}

$.fn.validateTextLabel = function () {
  return this.validate({
    rules: {
      text: {
        required: generate_disabled
      }
    }
  });
};

$.fn.validateIntegerLabel = function () {
  return this.validate({
    rules: {
      integer: {
        required: generate_disabled,
        number: true
      }
    }
  });
};

$.fn.validateUrlLabel = function () {
	return this.validate({
    rules: {
      url: {
        required: generate_disabled,
        url: true
      }
    }
  });
};

$.fn.validatePositionLabel = function () {
  return this.validate({
    rules: {
      start: {
        required: generate_disabled,
        number: true
      },
      length: {
        required: generate_disabled,
        number: true
      }
    }
  });
};

$.fn.validateRefLabel = function () {
  return this.validate({
    rules: {
      ref: {
        required: generate_disabled
      }
    }
  });
};

function change_label_ref(place, form)
{
  return function (row) {
    var input_show = $('input[name=ref]', form);
    var input_hide = $('input[name=hidden_ref]', form);

    input_show.attr('value', row.name);
    input_hide.attr('value', row.id);

    place.gridHighLight(row.id);
    
    return false;
  };
}
