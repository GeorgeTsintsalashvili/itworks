
<script type="text/javascript">

$(".view-page").click(function(event){

     event.preventDefault();

		 let address = $(this).attr("href");
		 let options = new Object();

		 let successCallback = (data) => $("#main-content").html(data);
		 let errorCallback = (xhr, status, error) => console.log(xhr.responseText);

		 options["type"] = "GET";
		 options["url"] = address;
		 options["error"] = errorCallback;
		 options["success"] = successCallback;

		 jQuery.ajax(options);
});

// initialize checkboxes

const maxPrice = Number.parseInt($("#max-price").attr("data-price"));
const minPrice = Number.parseInt($("#min-price").attr("data-price"));

$(".icheck input").iCheck({
   checkboxClass: "icheckbox_minimal-blue",
   radioClass: "iradio_minimal-blue"
});

// initialize touchspin

$("input#touchspin1").TouchSpin({
    min: minPrice,
    step: 1,
    decimals: 0,
    max: maxPrice,
    postfix: "₾"
});

$("input#touchspin2").TouchSpin({
    min: 0,
    step: 1,
    max: maxPrice - 1,
    postfix: "₾"
});

$("input#touchspin3").TouchSpin({
    verticalbuttons: true,
    min: 1,
    verticalupclass: "fas fa-chevron-up",
    verticaldownclass: "fas fa-chevron-down"
});

// initialize text editor

tinymce.baseURL = "/admin/plugins/tinymce/";

tinymce.init({
  selector: ".kt-tinymce",
  plugins: "lists link table hr searchreplace advlist",
  resize: true,
  height: 500,
  contextmenu: "cut copy paste",
  fontsize_formats: "12px 14px 16px 18px 20px 24px 26px 28px 32px 36px 42px",
  toolbar: "undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic underline strikethrough superscript subscript blockquote | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | table hr link removeformat",
	setup: (editor) => editor.on("change", () => tinymce.triggerSave())
});

// request processing queue control variable

var waitingForResponse = false;
var invalidFieldExists = false;

// file restrictions

var allowedExtensions = ["png", "jpg", "jpeg"];
var maxFileSize = 1048576;
var mainImageIsValid = false;

// store success callback

function storeSuccessCallback(data)
{
  let message = data["stored"] ? "დამატება შესრულდა" : "დამატება არ შესრულდა";

  if(data["stored"])
  {
    let address = $("#main-content").attr("data-page-address");

    refreshPage("POST", address);
  }

  $(".loader").removeClass("visible").addClass("invisible");

  printMessage(message);

  waitingForResponse = false;
}

// check fields

function checkStoreFormFields()
{
  let form = document.querySelector(".store-form");
  let textInputs = form.querySelectorAll("input[name][type=text]:not(.part-select-field),select[name],textarea[name=description]");
  let invalidField = null;

  invalidFieldExists = false;

  for(let element of textInputs)
  {
    let str = $(element).prop("value").trim();

    if(str.length == 0)
    {
      invalidField = element;

      invalidFieldExists = true;

      break;
    }

    else if($(element).attr("data-reg-exp"))
    {
       let regExStr = $(element).attr("data-reg-exp");
       let regEx = new RegExp(regExStr);

       if(!regEx.test(str))
       {
         invalidField = element;
         invalidFieldExists = true;

         break;
       }
    }
  }

  if(invalidFieldExists)
  {
    $(invalidField).focus();
  }
}

// store product form submit handler

function storeFormSubmitHandler(event)
{
	event.preventDefault();

  checkStoreFormFields.call(this);

  if(!waitingForResponse && !invalidFieldExists)
  {
    if(mainImageIsValid)
    {
      waitingForResponse = true;

      let options = new Object();
      let progressLoader = $(this).find(".loader");

      $(progressLoader).removeClass("invisible").addClass("visible");

      options["type"] = $(this).attr("method");
      options["processData"] = false;
      options["contentType"] = false;
      options["url"] = $(this).attr("action");
      options["error"] = (jqXHR, textStatus, errorThrown) => console.log(jqXHR.responseText);
      options["data"] = new FormData(this);
      options["success"] = storeSuccessCallback;

      jQuery.ajax(options);
    }

    else printMessage("აირჩიეთ სურათი დაშვებული ზომითა და გაფართოებით");
  }
}

// bind handler

$(".store-form").submit(storeFormSubmitHandler);

// main image input change handler

function fileChangeHandler()
{
   if(this.files.length)
   {
     let fileSize = this.files[0].size;
     let name = this.files[0].name;
     let nameParts = name.split(".");
     let type = nameParts[nameParts.length - 1].toLowerCase();

     if(fileSize == 0 || fileSize > maxFileSize)
     {
       mainImageIsValid = true;

       printMessage("ფაილის ზომა არ არის დაშვებული");
     }

     else if(!allowedExtensions.includes(type))
     {
       printMessage("აირჩიეთ დაშვებული გაფართოების ფაილი");
     }

     else mainImageIsValid = true;
   }

   else printMessage("აირჩიეთ მთავარი სურათი");
}

$(".store-form input[name=mainImage]").change(fileChangeHandler);

</script>
