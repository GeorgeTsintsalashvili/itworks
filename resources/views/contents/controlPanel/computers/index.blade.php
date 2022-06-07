
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="font-6 p-abs">სისტემური ბლოკები</h4>
				<div class="options">
				 <ul class="nav nav-tabs">
					 <li class="active">
						 <a href="#products-list" data-toggle="tab" class="font-6">ჩამონათვალი</a>
					 </li>
					 <li>
						 <a href="#add-product" data-toggle="tab" class="font-6">დამატება</a>
					 </li>
				 </ul>
				</div>
			</div>
			<div class="panel-body">

				<div class="tab-content">

 				 <div class="tab-pane active" id="products-list">
					 <table class="table table-hover mb1" data-update-route="{{ route('sbUpdateBase') }}">
						 <thead>
							 <tr class="font-6">
								 <th>სურათი</th>
								 <th>სათაური</th>
								 <th>ფასი</th>
								 <th>ფასდაკლება</th>
								 <th>მდგომარეობა</th>
								 <th>საწყობის ტიპი</th>
								 <th class="text-center">ხილულობა</th>
 								 <th class="text-center">შემოთავაზება</th>
 								 <th class="text-center">ნახვა</th>
 								 <th class="text-center">წაშლა</th>
							 </tr>
						 </thead>
						 <tbody>

							@foreach($items as $value)

							 <tr data-record-id="{{ $value -> id }}" class="<?=$value -> affected ? 'affected-product' : 'not-affected-product' ?>">

								 <td class="preview-image">
									 <img src="/images/computers/main/preview/{{ $value -> mainImage }}">
								 </td>

								 <td>
									 <input data-parameter-name="title" title="{{ $value -> title }}" type="text" value="{{ $value -> title }}" class="no-border transparent-bg-color field">
								 </td>

								 <!--- price and discount fields start --->
								 <td>
									 <b> ₾ </b>
									 <input data-parameter-name="price" class="wpx-50 no-border transparent-bg-color field" value="{{ $value -> price }}">
								 </td>

								 <td>
									 <b> ₾ </b>
									 <input data-parameter-name="discount" class="wpx-50 no-border transparent-bg-color field" value="{{ $value -> discount }}">
								 </td>

								 <!--- price and discount fields end ---->
								 <td>
									 <select data-parameter-name="conditionId" class="wpr-100 record-update-data-list field">
										 @foreach($conditions as $conditon)
											 <option class="font-6" {{ $value -> conditionId == $conditon -> id ? "selected" : null }} value="{{ $conditon -> id }}">{{ $conditon -> conditionTitle }}</option>
										 @endforeach
									 </select>
								 </td>

								 <td>
									 <select data-parameter-name="stockTypeId" class="wpr-100 record-update-data-list field">
										 @foreach($stockTypes as $stock)
											 <option class="font-6" {{ $value -> stockTypeId == $stock -> id ? "selected" : null }} value="{{ $stock -> id }}">{{ $stock -> stockTitle }}</option>
										 @endforeach
									 </select>
								 </td>

								 <td class="text-center">
									 <input data-parameter-name="visibility" class="record-update-switch field" data-size="mini" type="checkbox" {{ $value -> visibility != 0 ? "checked" : null }} value="{{ $value -> visibility }}">
								 </td>

								 <td class="text-center">
									 <input data-parameter-name="isOffer" class="record-update-switch field" data-size="mini" type="checkbox" {{ $value -> isOffer != 0 ? "checked" : null }} value="{{ $value -> isOffer }}">
								 </td>

								 <td class="text-center">
									<a class="view-page" href="{{ route('sbEdit', ['id' => $value -> id]) }}">
									 <i style="font-size: 18px" class="fas fa-edit" aria-hidden="true"></i>
									</a>
								 </td>

								 <td class="text-center">
									 <a class="delete-record" href="{{ route('sbDestroy', ['id' => $value -> id]) }}">
										 <i style="font-size: 16px" class="fas fa-trash-alt" aria-hidden="true"></i>
									 </a>
								 </td>

							 </tr>

							@endforeach

						</tbody>
					 </table>

						<div data-page-key="{{ $paginationKey }}" data-current-page="{{ $paginator -> currentPage }}" class="clearfix pagination-column col-sm-12">

							<div class="col-sm-3">
								<div class="input-group">
									<span class="input-group-addon" id="search-button">
										<i class="fas fa-search"></i>
									</span>
									<input id="search-field" type="text" autocomplete="off" data-control-parameter-name="search-query" value="{{ $searchQuery }}" class="form-control font-7 control-field" placeholder="საძიებო სიტყვა">
								</div>
							 </div>

							<select class="control-data-list col-sm-2 p-n mr20" data-control-parameter-name="{{ $systemsKey }}">
								<option class="font-6" value="0">ყველა სისტემა</option>
								 @foreach($systems as $system)
									 <option value="{{ $system -> id }}" {{ $selectedSystemId == $system -> id ? "selected" : null }} class="font-6"> {{ $system -> seriesTitle }} </option>
								 @endforeach
							</select>

							<select class="control-data-list col-sm-2 p-n mr20" data-control-parameter-name="{{ $sortKey }}">
								 @foreach($sortData as $key => $sortItem)
									 <option value="{{ $key }}" {{ $selectedSortOrder == $key ? "selected" : null }} class="font-6"> {{ $sortItem }} </option>
								 @endforeach
							</select>

							<ul class="pagination mt0 mb20 col-sm-4">

									@if($paginator -> currentPage > 1)

									 <li>
										<a href="{{ $paginator -> currentPage - 1 }}" class="page-switch">
											<i class="fa fa-angle-double-left" aria-hidden="true"></i>
										</a>
									 </li>

									@endif

									@foreach($paginator -> pages as $page)

									@if($page['isPad'])

									<li>
										<span>{{ $page['value'] }}</span>
									</li>

									@elseif($page['isActive'])

									<li class="active">
										<span>{{ $page['value'] }}</span>
									</li>

									@else

									<li>
									 <a href="{{ $page['value'] }}" class="page-switch">{{ $page['value'] }}</a>
									</li>

									@endif

									@endforeach

									@if($paginator -> currentPage < $paginator -> maxPage)

									<li>
										<a href="{{ $paginator -> currentPage + 1 }}" class="page-switch">
											<i class="fa fa-angle-double-right" aria-hidden="true"></i>
										</a>
									</li>

									@endif

							 </ul>

						</div>

						<div class="row p10">
		          <div class="col-sm-6">
		            <div class="alert alert-dismissable alert-warning">
		        				<p class="font-6">
		                  <i class="fa fa-exclamation-triangle"></i>
		                  <span style="font-size: 14px">საქონლის მინიმალური დაშვებული ფასი - </span>
		                  <b id="min-price" data-price="{{ $minPrice }}"> {{ $minPrice }} ლარი</b>
		                </p>
		                <p class="font-6">
										 <i class="fa fa-exclamation-triangle"></i>
		                 <span style="font-size: 14px">საქონლის მაქსიმალური დაშვებული ფასი - </span>
		                 <b id="max-price" data-price="{{ $maxPrice }}"> {{ $maxPrice }} ლარი</b>
		                </p>
		        		</div>
		         </div>
		        </div>

 				 </div>

        <div class="tab-pane" id="add-product">
					<form action="{{ route('sbStore') }}" method="POST" class="form-horizontal row-border store-form" enctype="multipart/form-data">
						<div class="form-group">

							<label class="col-sm-1 control-label font-6">სათაური</label>
							<div class="col-sm-3">
								<div class="input-group font-7">
									<span class="input-group-addon icheck">
										<input type="checkbox" name="isOffer">
									</span>
									<input type="text" name="title" class="form-control font-7">
								</div>
							</div>

							<label class="col-sm-1 control-label font-6">ფასი</label>
 						  <div class="col-sm-3">
 			         <input type="text" id="touchspin1" name="price" class="form-control font-7 system-price">
 			        </div>

						  <label class="col-sm-1 control-label font-6">ფასდ-ბა</label>
							 <div class="col-sm-3">
								<input type="text" value="0" name="discount" class="form-control font-7" id="touchspin2">
							 </div>

						</div>

						<div class="form-group">

							<label class="col-sm-1 control-label font-6">ვადა</label>
							<div class="col-sm-3">
								 <select name="warrantyId" class="form-select wpr-100">
									 @foreach($warranties as $value)
										 <option class="font-6" value="{{ $value -> id }}">{{ $value -> durationUnit }}</option>
									 @endforeach
								 </select>
							</div>

							<label class="col-sm-1 control-label font-6">მდგო-ბა</label>
							<div class="col-sm-3">
								<select name="conditionId" class="form-select wpr-100">
									@foreach($conditions as $value)
		                <option class="font-6" value="{{ $value -> id }}">{{ $value -> conditionTitle }}</option>
									@endforeach
								</select>
							</div>
							<label class="col-sm-1 control-label font-6">საწყობი</label>
							<div class="col-sm-3">
								<select name="stockTypeId" class="form-select wpr-100">
									@foreach($stockTypes as $value)
		                <option class="font-6" value="{{ $value -> id }}">{{ $value -> stockTitle }}</option>
									@endforeach
								</select>
							</div>
						 </div>

						 <div class="form-group">

						 <label class="col-sm-1 control-label font-6">გარანტია</label>
						 <div class="col-sm-3">
								<input type="text" name="warrantyDuration" class="form-control font-7" value="1" id="touchspin3">
						 </div>

						 <label class="col-sm-1 control-label font-6">სურათი</label>
						 <div class="col-sm-3">
							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
	 							<div class="input-group">
	 								<div class="form-control uneditable-input" data-trigger="fileinput">
	 									<i class="fas fa-upload file-upload-icon"></i>
										<span class="fileinput-filename">Max. 1 MB</span>
	 								</div>
	 								<span class="input-group-btn">
	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
											<i class="fas fa-times"></i>
										</a>
	 									<span class="btn btn-default btn-file">
	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
	 										<input type="file" name="mainImage">
	 									</span>
	 								</span>
	 							</div>
	 						</div>
						 </div>

						 <label class="col-sm-1 control-label font-6">ხილულობა</label>
 						 <div class="col-sm-3">
 							<select name="visibility" class="form-select wpr-100">
 								<option class="font-6" value="1">გამოჩნდეს საიტზე</option>
 								<option class="font-6" value="0">არ გამოჩნდეს საიტზე</option>
 							</select>
						 </div>

						</div>

						<div class="form-group">

						 <label class="col-sm-1 control-label font-6">სურათები</label>
 						 <div class="col-sm-3">
 							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
 	 							<div class="input-group">
 	 								<div class="form-control uneditable-input" data-trigger="fileinput">
 	 									<i class="fas fa-upload file-upload-icon"></i>
 										<span class="fileinput-filename">Max. 1 MB</span>
 	 								</div>
 	 								<span class="input-group-btn">
 	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
 											<i class="fas fa-times"></i>
 										</a>
 	 									<span class="btn btn-default btn-file">
 	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
 	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
 	 										<input type="file" name="images[]">
 	 									</span>
 	 								</span>
 	 							</div>
 	 						</div>
 						 </div>

						 <label class="col-sm-1 control-label font-6"> </label>
 						 <div class="col-sm-3">
 							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
 	 							<div class="input-group">
 	 								<div class="form-control uneditable-input" data-trigger="fileinput">
 	 									<i class="fas fa-upload file-upload-icon"></i>
 										<span class="fileinput-filename">Max. 1 MB</span>
 	 								</div>
 	 								<span class="input-group-btn">
 	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
 											<i class="fas fa-times"></i>
 										</a>
 	 									<span class="btn btn-default btn-file">
 	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
 	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
 	 										<input type="file" name="images[]">
 	 									</span>
 	 								</span>
 	 							</div>
 	 						</div>
 						 </div>

						 <label class="col-sm-1 control-label font-6"></label>
 						 <div class="col-sm-3">
 							 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
 	 							<div class="input-group">
 	 								<div class="form-control uneditable-input" data-trigger="fileinput">
 	 									<i class="fas fa-upload file-upload-icon"></i>
 										<span class="fileinput-filename">Max. 1 MB</span>
 	 								</div>
 	 								<span class="input-group-btn">
 	 									<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
 											<i class="fas fa-times"></i>
 										</a>
 	 									<span class="btn btn-default btn-file">
 	 										<span class="fileinput-new upload-button font-6">არჩევა</span>
 	 										<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
 	 										<input type="file" name="images[]">
 	 									</span>
 	 								</span>
 	 							</div>
 	 						</div>
 						 </div>

						 <label class="col-sm-1 control-label font-6"></label>
							 <div class="col-sm-3">
								 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
									<div class="input-group">
										<div class="form-control uneditable-input" data-trigger="fileinput">
											<i class="fas fa-upload file-upload-icon"></i>
											<span class="fileinput-filename">Max. 1 MB</span>
										</div>
										<span class="input-group-btn">
											<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
												<i class="fas fa-times"></i>
											</a>
											<span class="btn btn-default btn-file">
												<span class="fileinput-new upload-button font-6">არჩევა</span>
												<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
												<input type="file" name="images[]">
											</span>
										</span>
									</div>
								</div>
							 </div>

							 <label class="col-sm-1 control-label font-6"></label>
								 <div class="col-sm-3">
									 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
										<div class="input-group">
											<div class="form-control uneditable-input" data-trigger="fileinput">
												<i class="fas fa-upload file-upload-icon"></i>
												<span class="fileinput-filename">Max. 1 MB</span>
											</div>
											<span class="input-group-btn">
												<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
													<i class="fas fa-times"></i>
												</a>
												<span class="btn btn-default btn-file">
													<span class="fileinput-new upload-button font-6">არჩევა</span>
													<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
													<input type="file" name="images[]">
												</span>
											</span>
										</div>
									</div>
								 </div>

								 <label class="col-sm-1 control-label font-6"></label>
									 <div class="col-sm-3">
										 <div class="fileinput fileinput-new wpr-100" data-provides="fileinput">
											<div class="input-group">
												<div class="form-control uneditable-input" data-trigger="fileinput">
													<i class="fas fa-upload file-upload-icon"></i>
													<span class="fileinput-filename">Max. 1 MB</span>
												</div>
												<span class="input-group-btn">
												<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">
													<i class="fas fa-times"></i>
												</a>
												<span class="btn btn-default btn-file">
												<span class="fileinput-new upload-button font-6">არჩევა</span>
												<span class="fileinput-exists file-change-button font-6">შეცვლა</span>
												<input type="file" name="images[]">
											</span>
										</span>
									</div>
								</div>
							</div>

						</div>

						<div class="form-group" style="border-top: 2px solid #c3c2c2">
							<label class="col-sm-1 control-label font-6">აღწერა</label>
							<div class="col-sm-11">
							 <textarea id="parts-editor" class="kt-tinymce" name="description"></textarea>
							</div>
						</div>

            <div id="system-full-description-container" style="display: none">
							<div id="header-template"></div>
							<div id="description-body"></div>
							<div id="footer-template"></div>
							<input type="hidden" name="header-template" class="header-template" value="">
							<input type="hidden" name="footer-template" class="footer-template" value="">
						</div>

            <div class="hidden-header-templates">
						 @foreach($headerTemplates as $template)
						 <div class="hidden-template" id="hidden-header-template-{{ $template -> id }}" style="display: none">{!! $template -> description !!}</div>
						 @endforeach
					  </div>

						<div class="hidden-footer-templates">
						 @foreach($footerTemplates as $template)
						 <div class="hidden-template" id="hidden-footer-template-{{ $template -> id }}" style="display: none">{!! $template -> description !!}</div>
						 @endforeach
					  </div>

						<div class="form-group">
							<label class="col-sm-1 control-label font-6">შაბლონები</label>
							<div class="col-sm-5">
								 <select class="form-select wpr-100" id="header-template-select">
									 <option class="font-6" value="0">ზედა შაბლონი არის ცარიელი</option>
									 @foreach($headerTemplates as $template)
									 <option class="font-6" value="{{ $template -> id }}">{{ $template -> title }}</option>
									 @endforeach
								 </select>
							</div>
							<div class="col-sm-5 col-sm-offset-1">
								 <select class="form-select wpr-100" id="footer-template-select">
									 <option class="font-6" value="0">ქვედა შაბლონი არის ცარიელი</option>
									 @foreach($footerTemplates as $template)
									 <option class="font-6" value="{{ $template -> id }}">{{ $template -> title }}</option>
									 @endforeach
								 </select>
							</div>
						</div>

						<div class="form-group part-details-block" id="part-processor" data-title-prefix="CPU">
						 <label class="col-sm-1 control-label font-6">პროცესორი</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-processor-title" data-part-block="part-processor">
							<input type="hidden" name="part-processor-id" value="0" class="system-part-id">
							<input type="hidden" name="part-processor-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-processor-price" data-part-block="part-processor">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-processor" data-parts-address="{{ route('cpu') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-processor">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-motherboard" data-title-prefix="MoBo">
						 <label class="col-sm-1 control-label font-6">დედაპლატა</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-motherboard-title" data-part-block="part-motherboard">
							<input type="hidden" name="part-motherboard-id" value="0" class="system-part-id">
							<input type="hidden" name="part-motherboard-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-motherboard-price" data-part-block="part-motherboard">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-motherboard"  data-parts-address="{{ route('mb') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-motherboard">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-ram" data-title-prefix="RAM">
						 <label class="col-sm-1 control-label font-6">ოპერატიული</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-ram-title" data-part-block="part-ram">
							<input type="hidden" name="part-ram-id" value="0" class="system-part-id">
							<input type="hidden" name="part-ram-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-ram-price" data-part-block="part-ram">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-ram" data-parts-address="{{ route('mm') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-ram">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-videocard" data-title-prefix="GPU">
						 <label class="col-sm-1 control-label font-6">გრაფიკა</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-videocard-title" data-part-block="part-videocard">
							<input type="hidden" name="part-videocard-id" value="0" class="system-part-id">
							<input type="hidden" name="part-videocard-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-videocard-price" data-part-block="part-videocard">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-videocard" data-parts-address="{{ route('vc') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-videocard">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-hdd" data-title-prefix="HDD">
						 <label class="col-sm-1 control-label font-6">HDD</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-hdd-title" data-part-block="part-hdd">
							<input type="hidden" name="part-hdd-id" value="0" class="system-part-id">
							<input type="hidden" name="part-hdd-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-hdd-price" data-part-block="part-hdd">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-hdd" data-parts-address="{{ route('hdd') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-hdd">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-ssd" data-title-prefix="SSD">
						 <label class="col-sm-1 control-label font-6">SSD</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-ssd-title" data-part-block="part-ssd">
							<input type="hidden" name="part-ssd-id" value="0" class="system-part-id">
							<input type="hidden" name="part-ssd-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-ssd-price" data-part-block="part-ssd">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-ssd" data-parts-address="{{ route('ssd') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-ssd">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-power-supply" data-title-prefix="PSU">
						 <label class="col-sm-1 control-label font-6">კვება</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-power-supply-title" data-part-block="part-power-supply">
							<input type="hidden" name="part-power-supply-id" value="0" class="system-part-id">
							<input type="hidden" name="part-power-supply-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-power-supply-price" data-part-block="part-power-supply">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-power-supply" data-parts-address="{{ route('ps') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-power-supply">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-case" data-title-prefix="Case">
						 <label class="col-sm-1 control-label font-6">კეისი</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-case-title" data-part-block="part-case">
							<input type="hidden" name="part-case-id" value="0" class="system-part-id">
							<input type="hidden" name="part-case-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-case-price" data-part-block="part-case">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-case" data-parts-address="{{ route('cases') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-case">
						 </div>
						</div>

						<div class="form-group part-details-block" id="part-cooler" data-title-prefix="FAN">
						 <label class="col-sm-1 control-label font-6">ქულერი</label>
						 <div class="col-sm-4">
							<input type="text" class="part-select-field part-title-field font-7" name="part-cooler-title" data-part-block="part-cooler">
							<input type="hidden" name="part-cooler-id" value="0" class="system-part-id">
							<input type="hidden" name="part-cooler-stock-type-id" value="0" class="system-part-stock-type-id">
						 </div>
						 <label class="col-sm-1 control-label font-6">ფასი</label>
						 <div class="col-sm-2">
							<input type="text" class="part-select-field part-price-field font-7" name="part-cooler-price" data-part-block="part-cooler">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-select-button pull-right" value="ნაწილის არჩევა" data-part-block="part-cooler" data-parts-address="{{ route('pc') }}">
						 </div>
						 <div class="col-sm-2">
							<input type="button" class="btn-primary btn font-6 part-remove-button pull-right" value="მოშორება" data-part-block="part-cooler">
						 </div>
						</div>

						<div class="form-group" style="border-top: 2px solid #c3c2c2">
							<label class="col-sm-1 control-label font-6">SEO</label>
							<div class="col-sm-5">
								<textarea name="seoKeywords" rows="5" placeholder="საგასაღებო სიტყვები" class="form-control font-7"></textarea>
							</div>
							<label class="col-sm-1 control-label font-6"> </label>
							<div class="col-sm-5">
								<textarea name="seoDescription" rows="5" placeholder="მოკლე აღწერა" class="form-control font-7"></textarea>
							</div>
						</div>

						<div class="form-group">

							<label class="col-sm-1 control-label font-6">CPU</label>
							<div class="col-sm-5">
								<input type="text" class="form-control font-7" name="cpu">
							</div>

						 <label class="col-sm-1 control-label font-6">System</label>
 						 <div class="col-sm-2">
 								<select name="seriesId" class="form-select wpr-100">
 									 @foreach($systems as $system)
 										<option class="font-6" value="{{ $system -> id }}">{{ $system -> seriesTitle }}</option>
 									 @endforeach
 								</select>
 						 </div>

						 <label class="col-sm-1 control-label font-6">Graphics</label>
						 <div class="col-sm-2">
								<select name="computerGraphicsId" class="form-select wpr-100">
									@foreach($computerGraphics as $value)
									 <option class="font-6" value="{{ $value -> id }}">{{ $value -> graphicsTitle }}</option>
									@endforeach
								</select>
						</div>

						</div>

						<div class="form-group">
						 <label class="col-sm-1 control-label font-6">GPU</label>
 		         <div class="col-sm-5">
 		          <input type="text" class="form-control font-7" name="gpuTitle">
 		         </div>
						 <label class="col-sm-1 control-label font-6">RAM</label>
						 <div class="col-sm-2">
							<input type="text" class="form-control font-7" data-reg-exp="^([1-9]\d*)$" name="memory">
						 </div>
						 <label class="col-sm-1 control-label font-6">VRAM</label>
 		         <div class="col-sm-2">
 		          <input type="text" data-reg-exp="^(0|[1-9]\d*)$" class="form-control font-7" name="videoMemory" value="0">
 		         </div>
						</div>

            <div class="form-group">
							<label class="col-sm-1 control-label font-6">HDD</label>
							<div class="col-sm-2">
								<input type="text" class="form-control font-7" data-reg-exp="^(0|[1-9]\d*)$" name="hardDiscDriveCapacity">
							</div>
							<label class="col-sm-1 control-label font-6">SSD</label>
							<div class="col-sm-2">
								<input type="text" class="form-control font-7" data-reg-exp="^(0|[1-9]\d*)$" name="solidStateDriveCapacity">
							</div>
							<label class="col-sm-1 control-label font-6">რაოდენობა</label>
							<div class="col-sm-2">
							 <input type="text" data-reg-exp="^(0|[1-9]\d*)$" class="form-control font-7" name="quantity">
							</div>
						</div>

						<div class="panel-footer">
							<div class="row">
								<div class="col-sm-2 col-sm-offset-1">
									<input type="submit" class="btn-primary btn font-6" value="სისტემის დამატება">
								</div>
								<div class="col-sm-2">
									<input type="button" class="btn-primary btn font-6" value="აღწერის დაკოპირება" id="copy-to-clipboard-button">
								</div>
								<div class="col-sm-3 col-sm-offset-0">
									<img height="60px" src="/images/general/admin-loader.svg" class="loader invisible mt-md n">
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

<input type="hidden" id="stock-check-str" value="{{ $stockCheckStr }}">

<div id="iframe-overlay" style="display: none">
 <span id="window-close-button">
	<i class="fas fa-times"></i>
 </span>
 <iframe id="part-select-window"></iframe>
</div>

@include('parts.controlPanel.parameters')
@include('parts.controlPanel.switches')
@include('parts.controlPanel.lists')
@include('parts.controlPanel.plugins.tinymce')
@include('parts.controlPanel.indexPage')
@include('parts.controlPanel.computerDescriptionBuilder')
