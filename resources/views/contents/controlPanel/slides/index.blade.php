
	<div class="row">

  <!--- images list --->

		<div class="col-xs-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="font-3">სლაიდების ჩამონათვალი</h4>
				</div>
				<div class="panel-body">
					<table class="table table-hover mb50" data-update-route="{{ route('updateSlideOrder') }}">
						<thead>
							<tr class="font-6">
								<th>სურათი</th>
								<th>ნომერი</th>
								<th>ნახვა</th>
								<th>წაშლა</th>
							</tr>
						</thead>
						<tbody>

	          @foreach($slides as $value)

							<tr data-record-id="{{ $value -> id }}">

								<td class="preview-image">
									<img src="/images/slides/preview/{{ $value -> image }}" data-at2x="/images/slides/original/{{ $value -> image }}">
								</td>

								<td>
									<input data-parameter-name="orderNum" class="no-border transparent-bg-color field" type="text" value="{{ $value -> orderNum }}">
								</td>

								<td>
									<a href="/images/slides/original/{{ $value -> image }}" data-fancybox-group="button" class="fancy fancybox-buttons ">
										<i class="fas fa-file-image"></i>
									</a>
							  </td>

								<td>
                  <a class="delete-record" href="{{ route('destroySlide', [ 'id' => $value -> id ]) }}">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </td>
							</tr>

	          @endforeach

					 </tbody>
					</table>

					<form class="form-horizontal files-upload-form" method="post" action="{{ route('storeSlides') }}" enctype="multipart/form-data">

						<div class="form-group mb0">

   						 <div class="col-sm-6">
   							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
   	 							<div class="input-group">
   	 								<div class="form-control uneditable-input" data-trigger="fileinput">
   	 									<i class="fas fa-upload file-upload-icon"></i>
   										<span class="fileinput-filename">Max. 3 MB</span>
   	 								</div>
   	 								<span class="input-group-btn">
   	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
   											<i class="fas fa-times"></i>
   										</a>
   	 									<span class="btn btn-default btn-file">
   	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
   	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
   	 										<input type="file" name="files[]">
   	 									</span>
   	 								</span>
   	 							</div>
   	 						</div>
   						 </div>

   						 <div class="col-sm-6">
   							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
   	 							<div class="input-group">
   	 								<div class="form-control uneditable-input" data-trigger="fileinput">
   	 									<i class="fas fa-upload file-upload-icon"></i>
   										<span class="fileinput-filename">Max. 3 MB</span>
   	 								</div>
   	 								<span class="input-group-btn">
   	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
   											<i class="fas fa-times"></i>
   										</a>
   	 									<span class="btn btn-default btn-file">
   	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
   	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
   	 										<input type="file" name="files[]">
   	 									</span>
   	 								</span>
   	 							</div>
   	 						</div>
   						 </div>

 					   </div>

	         <div class="form-group mb0">

  						 <div class="col-sm-6">
  							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
  	 							<div class="input-group">
  	 								<div class="form-control uneditable-input" data-trigger="fileinput">
  	 									<i class="fas fa-upload file-upload-icon"></i>
  										<span class="fileinput-filename">Max. 3 MB</span>
  	 								</div>
  	 								<span class="input-group-btn">
  	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
  											<i class="fas fa-times"></i>
  										</a>
  	 									<span class="btn btn-default btn-file">
  	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
  	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
  	 										<input type="file" name="files[]">
  	 									</span>
  	 								</span>
  	 							</div>
  	 						</div>
  						 </div>

  						 <div class="col-sm-6">
  							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
  	 							<div class="input-group">
  	 								<div class="form-control uneditable-input" data-trigger="fileinput">
  	 									<i class="fas fa-upload file-upload-icon"></i>
  										<span class="fileinput-filename">Max. 3 MB</span>
  	 								</div>
  	 								<span class="input-group-btn">
  	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
  											<i class="fas fa-times"></i>
  										</a>
  	 									<span class="btn btn-default btn-file">
  	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
  	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
  	 										<input type="file" name="files[]">
  	 									</span>
  	 								</span>
  	 							</div>
  	 						</div>
  						 </div>

					   </div>

						 <div class="panel-footer">
							 <div class="row">
								 <div class="col-sm-3 col-sm-offset-0">
									 <input type="submit" class="btn-primary btn font-6" value="ატვირთვა">
								 </div>
								 <div class="col-sm-3 col-sm-offset-0 mt-md n">
									 <img class="invisible loader" height="60px" src="/images/general/admin-loader.svg">
								 </div>
 						 </div>
					 </div>

				</form>

			</div>
		</div>
	</div>

</div>

@include('parts.controlPanel.plugins.fancybox')

@include('parts.controlPanel.parameters')

@include('parts.controlPanel.slideImageUpload')

<script type="text/javascript">

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

</script>
