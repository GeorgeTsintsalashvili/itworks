
<script type="text/javascript">

// files upload form submit handler

function uploadFormSubmitHandler(e)
{
	e.preventDefault();

  let uploadLoader = $(this).find(".loader");
	let options = new Object();

	$(uploadLoader).removeClass("invisible").addClass("visible");

	options["type"] = $(this).attr("method");
  options["processData"] = false;
	options["contentType"] = false;
	options["url"] = $(this).attr("action");
	options["data"] = new FormData(this);
	options["success"] = uploadSuccessCallback;
	options["error"] = (jqXHR, textStatus, errorThrown) => console.log(jqXHR.responseText);
	options["loader"] = uploadLoader;

	jQuery.ajax(options);
}

// files upload success callback

function uploadSuccessCallback(data){

	let message = "ატვირთვა შესრულდა";
	let pageAddress = $("#main-content").attr("data-page-address");

	$(this).attr("loader").removeClass("visible").addClass("invisible");

	if(!data["uploaded"]) message = "ატვირთვა არ შესრულდა";

	else if(!data["testPassed"]) message = "ზომა ან გაფართოება არაა დაშვებული";

	else refreshPage("POST", pageAddress);

  printMessage(message, 3000);
}

// bind handlers

$(".files-upload-form").submit(uploadFormSubmitHandler);

</script>
