
<script type="text/javascript">

// print message logic

function printMessage(message, delay = 3000)
{
  var snackbar = document.getElementById("snackbar");
  var msgCont =  document.querySelector("#snackbar .message");

  $(msgCont).text(message);
  $(snackbar).addClass("show");

  window.setTimeout( () => $(snackbar).removeClass("show") || $(msgCont).text(""), delay);
}

// request send logic

function sendRequest(method, address, formData, successCallback, customErrorCallback = null)
{
  let options = new Object();
  let defaultErrorCallback = (xhr, status, error) => console.log(xhr.responseText);

  options["type"] = method;
  options["url"] = address;
  options["data"] = formData;
  options["error"] = customErrorCallback === null ? defaultErrorCallback : customErrorCallback;
  options["success"] = successCallback;

  jQuery.ajax(options);
}

</script>
