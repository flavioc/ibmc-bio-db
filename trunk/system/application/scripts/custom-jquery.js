function birthdayErrorPlacement(error, element)
{
  if(element.is("#birthday")) {
    error.appendTo(element.next().next());
  } else {
    error.appendTo(element.next());
  }
}

function basicErrorPlacement(error, element)
{
  error.appendTo(element.next());
}

$.blockLoadingUI = function () {
  return $.blockUI({ message: $('img#loading_image') });
};