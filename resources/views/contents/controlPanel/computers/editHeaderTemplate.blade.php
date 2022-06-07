
<div class="panel panel-default" id="product-data-container" data-record-id="{{ $template -> id }}">
 <div class="panel-heading">
  <h2 id="back-button">
   <i class="fas fa-long-arrow-alt-left"></i>
   <span class="font-3"> უკან დაბრუნება </span>
  </h2>
 </div>

 <div class="panel-body">
  <div class="tab-content">
   <div class="tab-pane active">
    <form class="form-horizontal record-update-form" method="POST" action="{{ route('headerTemplateUpdate') }}">
     <div class="form-group">
      <div class="col-sm-12">
       <input type="text" class="form-control font-7" name="title" value="{{ $template -> title }}" />
      </div>
     </div>

     <div class="form-group">
      <input type="hidden" name="record-id" value="{{ $template -> id}}" />
      <div class="col-sm-12">
       <textarea class="kt-tinymce" name="description">{{ $template -> description }}</textarea>
      </div>
     </div>

     <div class="panel-footer">
      <div class="row">
       <div class="col-sm-3">
        <input type="submit" class="btn-primary btn font-6" value="განახლება" />
       </div>
      </div>
     </div>
    </form>
   </div>
  </div>
 </div>
</div>

@include('parts.controlPanel.general')

@include('parts.controlPanel.plugins.tinymce')

<script type="text/javascript">

// back button click handler

function backButtonClickHandler()
{

  if (localStorage["pageAddress"])
  {
    requestPage(localStorage["pageAddress"]);
  }

  else window.location.reload();
}

$("#back-button").click(backButtonClickHandler);

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

</script>
