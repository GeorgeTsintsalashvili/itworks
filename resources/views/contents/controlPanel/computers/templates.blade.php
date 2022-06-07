
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6 p-abs">ზედა შაბლონები</h4>
				<div class="options">
				 <ul class="nav nav-tabs">
					 <li class="active">
						 <a href="#header-templates" data-toggle="tab" class="font-6">ჩამონათვალი</a>
					 </li>
					 <li>
						 <a href="#add-header-template" data-toggle="tab" class="font-6">დამატება</a>
					 </li>
				 </ul>
				</div>
			</div>
			<div class="panel-body">

				<div class="tab-content">

 				 <div class="tab-pane active" id="header-templates">
					 <table class="table table-hover mb1" data-update-route="{{ route('headerTemplateTitleUpdateBase') }}">
						 <thead>
							 <tr class="font-6">
								 <th>სათაური</th>
 								 <th class="text-center">რედაქტირება</th>
 								 <th class="text-center">წაშლა</th>
							 </tr>
						 </thead>
						 <tbody>

							@foreach($headerTemplates as $value)

							 <tr data-record-id="{{ $value -> id }}">

								 <td>
									 <input data-parameter-name="title" title="{{ $value -> title }}" type="text" value="{{ $value -> title }}" class="no-border transparent-bg-color field">
								 </td>

								 <td class="text-center">
									<a class="view-page" href="{{ route('headerTemplateEdit', ['id' => $value -> id]) }}">
									 <i style="font-size: 18px" class="fas fa-edit" aria-hidden="true"></i>
									</a>
								 </td>

								 <td class="text-center">
									 <a class="delete-record" href="{{ route('headerTemplateDestroy', ['id' => $value -> id]) }}">
										 <i style="font-size: 16px" class="fas fa-trash-alt" aria-hidden="true"></i>
									 </a>
								 </td>

							 </tr>

							@endforeach

						</tbody>
					 </table>

 				 </div>

        <div class="tab-pane" id="add-header-template">
					<form action="{{ route('headerTemplateStore') }}" method="POST" class="form-horizontal row-border store-form" enctype="multipart/form-data">

						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" name="title" class="form-control font-7" placeholder="შეიყვანეთ სათაური">
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12">
							 <textarea class="kt-tinymce" name="description" id="header-template-textarea"></textarea>
							</div>
						</div>

						<div class="panel-footer">
							<div class="row">
								<div class="col-sm-3">
									<input type="submit" class="btn-primary btn font-6" value="შაბლონის დამატება">
								</div>
							</div>
						</div>
					</form>
        </div>

			 </div>

			</div>
		</div>
  </div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6 p-abs">ქვედა შაბლონები</h4>
				<div class="options">
				 <ul class="nav nav-tabs">
					 <li class="active">
						 <a href="#footer-templates" data-toggle="tab" class="font-6">ჩამონათვალი</a>
					 </li>
					 <li>
						 <a href="#add-footer-template" data-toggle="tab" class="font-6">დამატება</a>
					 </li>
				 </ul>
				</div>
			</div>
			<div class="panel-body">

				<div class="tab-content">

 				 <div class="tab-pane active" id="footer-templates">
					 <table class="table table-hover mb1" data-update-route="{{ route('footerTemplateTitleUpdateBase') }}">
						 <thead>
							 <tr class="font-6">
								 <th>სათაური</th>
 								 <th class="text-center">რედაქტირება</th>
 								 <th class="text-center">წაშლა</th>
							 </tr>
						 </thead>
						 <tbody>

							@foreach($footerTemplates as $value)

							 <tr data-record-id="{{ $value -> id }}">

								 <td>
									 <input data-parameter-name="title" title="{{ $value -> title }}" type="text" value="{{ $value -> title }}" class="no-border transparent-bg-color field">
								 </td>

								 <td class="text-center">
									<a class="view-page" href="{{ route('footerTemplateEdit', ['id' => $value -> id]) }}">
									 <i style="font-size: 18px" class="fas fa-edit" aria-hidden="true"></i>
									</a>
								 </td>

								 <td class="text-center">
									 <a class="delete-record" href="{{ route('footerTemplateDestroy', ['id' => $value -> id]) }}">
										 <i style="font-size: 16px" class="fas fa-trash-alt" aria-hidden="true"></i>
									 </a>
								 </td>

							 </tr>

							@endforeach

						</tbody>
					 </table>

 				 </div>

        <div class="tab-pane" id="add-footer-template">
					<form action="{{ route('footerTemplateStore') }}" method="POST" class="form-horizontal row-border store-form" enctype="multipart/form-data">

						<div class="form-group">
							<div class="col-sm-12">
								<input type="text" name="title" class="form-control font-7" placeholder="შეიყვანეთ სათაური">
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12">
							 <textarea class="kt-tinymce" name="description" id="footer-template-textarea"></textarea>
							</div>
						</div>

						<div class="panel-footer">
							<div class="row">
								<div class="col-sm-3">
									<input type="submit" class="btn-primary btn font-6" value="შაბლონის დამატება">
								</div>
							</div>
						</div>
					</form>
        </div>

			 </div>

			</div>
		</div>
  </div>
</div>

@include('parts.controlPanel.parameters')

@include('parts.controlPanel.lists')

@include('parts.controlPanel.plugins.tinymce')

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

// store success callback

function storeSuccessCallback(data)
{
  let message = data["success"] ? "დამატება შესრულდა" : "დამატება არ შესრულდა";

  if(data["success"])
  {
    let address = $("#main-content").attr("data-page-address");

    refreshPage("POST", address);
  }

  printMessage(message);

  waitingForResponse = false;
}

// check fields

function checkStoreFormFields()
{
  let invalidField = null;

  invalidFieldExists = false;

  $(this).find("input[name][type=text]").each(function(index, element){

    let str = $(element).prop("value").trim();

    if(str.length == 0)
    {
      invalidField = element;

      invalidFieldExists = true;

      return false;
    }

    else if ($(element).attr("data-reg-exp"))
    {
       let regExStr = $(element).attr("data-reg-exp");
       let regEx = new RegExp(regExStr);

       if(!regEx.test(str))
       {
         invalidField = element;
         invalidFieldExists = true;

         return false;
       }
    }

  });

  if (!invalidFieldExists)
  {
    let textarea = $(this).find("textarea");

    if (tinymce.get($(textarea).attr("id")).getContent().trim().length == 0)
    {
      tinymce.get($(textarea).attr("id")).focus();

      invalidFieldExists = true;
    }

    else invalidFieldExists = false;
  }

  else $(invalidField).focus();
}

// store product form submit handler

function storeFormSubmitHandler(event)
{
	event.preventDefault();

  checkStoreFormFields.call(this);

  if(!waitingForResponse && !invalidFieldExists)
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
}

// bind handler

$(".store-form").submit(storeFormSubmitHandler);


</script>
