
<script type="text/javascript">

// back button click handler

function backButtonClickHandler()
{
  let method = localStorage.getItem("requestMethod");
  let address = localStorage.getItem("pageAddress");
  let formData = JSON.parse(localStorage.getItem("pageFormData"));

  refreshPage(method, address, formData)
}

$("#back-button").click(backButtonClickHandler);

// initialize text editor

// init text editor

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

// intialize lists

$(".edit-page-list").select2();

// initialize fancybox

let fancy = $(".fancy");

if(fancy.length)
{
   $(".fancybox").fancybox();

   let fancyOptions = { openEffect: "fade",
                        closeEffect: "fade",
                        loop: false,
                        prevEffect: "elastic",
                        nextEffect: "elastic",
                        closeBtn: false,
                        helpers: { title: { type: "inside" },
                                   buttons: {} } };

   $(".fancybox-buttons").fancybox(fancyOptions);
}

// initiliaze images carousel

var carousel = document.querySelector(".owl-carousel");

$(carousel).owlCarousel({
    loop:false,
    margin:10,
    dots:false,
    autoplay:false,
    nav:true,
    navText: ["<i class='fas fa-arrow-alt-circle-left'></i>",
              "<i class='fas fa-arrow-circle-right'></i>"],
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
});

// carousel image delete click handler

function imageDeleteHandler(event)
{
  event.preventDefault();

  var carouselItem = $(this).parents(".owl-item");
  let options = new Object();

  options["type"] = "GET";
  options["url"] = $(this).attr("href");
  options["error"] = (xhr, status, error) => console.log(xhr.responseText);
  options["success"] = function(data){

     let message = "სურათი არ წაიშალა";

     if(data["destroyed"])
     {
       message = "სურათი წაიშალა";

       var carouselData = $(carousel).data("owl.carousel");
       var carouslItems = carouselData._items;

       for(let index in carouslItems)
       {
         if($(carouselItem).is(carouslItems[index]))
         {
           $(carousel).trigger("remove.owl.carousel", index).trigger("refresh.owl.carousel");

           break;
         }
       }
     }

     printMessage(message);
  }

  jQuery.ajax(options);
}

// bind handlers

$(".image-delete-button").click(imageDeleteHandler);

// dropzone uploader complete event handler

function completeHandler(file)
{
  let maxSize = 1024 * 1024;

  if (file.size > maxSize) {

    printMessage("ფაილის დაშვებული მაქსიმალური ზომა არის 1 MB");

    return false;
  }

  if(!file.type.match('image/(jpg|jpeg|png)')) {

    printMessage("შეარჩიეთ სხვა გაფართოების ფაილი");

    return false;
  }
}

// initiliaze images dropzone options

let dropzoneImagesOptions = {
    maxFilesize: 1, // in megabytes
    uploadMultiple: false,
    paramName: "image",
    parallelUploads: 1,
    complete: completeHandler,
    sending: (file, xhr, formData) => formData.append("record-id", $("#product-data-container").attr("data-record-id")),
    acceptedFiles: ".png,.jpg,.jpeg",
    // uploadprogress: (file, progress, bytesSent) => console.log(file),
    error: (file, response) => console.log(response),
    success: function(file, response){

      let uploaded = response["uploaded"] && response["testPassed"];
      let message = uploaded ? "ფაილი აიტვირთა" : "ფაილი არ აიტვირთა";

      if(!uploaded) this.removeFile(file);

      else
      {
        let lastId = response["id"];
        let uploadRequestAddress = $("#images-dropzone").attr("action");

        let addressParts = uploadRequestAddress.split("/");
        let lastElementIndex = addressParts.length - 1;
        let newLastAddressPart = addressParts[lastElementIndex].replace("upload", "destroy");

        addressParts[lastElementIndex] = newLastAddressPart;
        addressParts[lastElementIndex + 1] = lastId;

        let imageDestroyAddress = addressParts.join("/");
        let newImage = document.createElement("img");

        $(newImage).attr("src", response["previewSrc"]);

        let carouselItem = document.createElement("div");
        let childItem = document.createElement("div");

        // let imageZoomAnchor = document.createElement("a");
        // let imageZoomIcon = document.createElement("i");

        let imageDestroyAnchor = document.createElement("a");
        let imageDestroyIcon = document.createElement("i");

        // $(imageZoomIcon).attr("class", "fas fa-expand");
        // $(imageZoomAnchor).attr("data-fancybox-group", response["originalSrc"]).addClass("fancy fancybox-buttons zoom-button").append(imageZoomIcon);

        $(imageDestroyIcon).attr("class", "fas fa-trash-alt");
        $(imageDestroyAnchor).attr("href", imageDestroyAddress).addClass("image-delete-button").append(imageDestroyIcon);

        $(childItem).attr("class", "item").append(newImage).append(imageDestroyAnchor);
        $(carouselItem).append(childItem);
        $(carousel).owlCarousel("add", carouselItem).owlCarousel("update");

        $(".image-delete-button").click(imageDeleteHandler);
      }

      printMessage(message);
   }
};

let imagesDropzone = new Dropzone("#images-dropzone", dropzoneImagesOptions);

// initialize image dropone

let options = {
    maxFilesize: 1,
    uploadMultiple: false,
  //  thumbnailWidth: 400,
  //  thumbnailHeight: 400,
    paramName: "image",
    parallelUploads: 1,
    acceptedFiles: ".png,.jpg,.jpeg",
    complete: completeHandler,
    sending: (file, xhr, formData) => formData.append("record-id", $("#product-data-container").attr("data-record-id")),
/*    thumbnail: function(file, dataUrl) {

       var reader = new FileReader();

       reader.addEventListener("load", function () {

         $("#main-image").attr("src", reader.result).height(350);

       }, false);

       reader.readAsDataURL(file);
    },  */
    // uploadprogress: (file, progress, bytesSent) => console.log(progress),
    error: (file, response) => (console.log(response)),
    success: function(file, response){

     let message = "სურათი განახლდა";

     if(!(response["uploaded"] && response["updated"])){

       this.removeFile(file);

       message = "სურათი არ განახლდა";
     }

     else $("#main-image").attr("src", response["newPreviewSrc"]);

     printMessage(message);
  }
};

let imageDropzone = new Dropzone("#main-image-dropzone", options);

// text data update success callback

function updateSuccess(response)
{
  let message = response["updated"] ? "განახლება შესრულდა" : "განახლება არ შესრულდა";

  printMessage(message);
}

// form submit handler

function updateRecord(event)
{
  event.preventDefault();

  let pageAddress = $(this).attr("action");
  let method = $(this).attr("method");
  let formData = $(this).serialize();

  sendRequest(method, pageAddress, formData, updateSuccess);
}

// bind handler

$(".record-update-form").submit(updateRecord);

// initialize icheck

$(".icheck input").iCheck({
   checkboxClass: "icheckbox_minimal-blue",
   radioClass: "iradio_minimal-blue"
});

</script>
