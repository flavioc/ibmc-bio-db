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

$.fn.validateObjLabel = function () {
  return this.validate({
    rules: {
      file: {
        required: generate_disabled
      }
    }
  });
};

$.fn.validateDateLabel = function () {
  return this.validate({
    rules: {
      date: {
        required: generate_disabled
      }
    },
    errorPlacement: function(label, element) {
      label.insertAfter(element.next());
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

$.fn.autocomplete_labels = function (type) {
  var url = get_app_url() + "/label/autocomplete_labels";
  return $(this).autocomplete(url,
                              {
                              minChars: 0,
                              delay: 400,
                              scroll: true,
                              selectFirst: false,
                              mustMatch: true,
                              extraParams: {type: type}
                              });
};

function get_label_by_name(name, fn)
{
  $.getJSON(get_app_url() + "/label/get_label_by_name/" + name, fn);
};

$.fn.datePickerDate = function () {
  return this.each(function () {
    $(this).datepicker({
      minDate: new Date(2009, 1-12, 1),
      maxDate: new Date(2038, 12-1, 31),
      defaultDate: new Date(),
      yearRange:'2008-2038',
      showOn: 'both',
      dateFormat: "dd-mm-yy",
      buttonImage: get_images_url() + "/calendar.gif"
    });
  });
};