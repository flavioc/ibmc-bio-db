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
