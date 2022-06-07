
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
      <div class="panel-heading">
				<h4 class="font-6 p-abs">შეკვეთები</h4>
				<div class="options">
				 <ul class="nav nav-tabs">
					 <li class="active">
						 <a href="#products-list" data-toggle="tab" class="font-6">ჩამონათვალი</a>
					 </li>
				 </ul>
				</div>
			</div>
			<div class="panel-body">
				<div class="tab-content">
				 <div class="tab-pane active" id="products-list">
					 <table class="table table-hover mb1" data-update-route="row update route">
	 					<thead>
	 						<tr class="font-6">
	 							<th>აიდი</th>
	 							<th>ღირებულება</th>
	 							<th>მიწოდების მეთოდი</th>
                <th>დამატების დრო</th>
                <th>გადახდის მეთოდი</th>
                <th>სტატუსი</th>
								<th class="text-center">ნახვა</th>
								<th class="text-center">წაშლა</th>
	 						</tr>
	 					</thead>
	 					<tbody>

	           @foreach($data['orders'] as $value)

	 						<tr data-record-id="{{ $value -> id }}">
	 							<td>
									<span> {{ $value -> id }} </span>
	 							</td>

	 							<td>
	 							 <b> ₾ </b>
	 							 <input data-parameter-name="order-price" class="wpx-50 no-border transparent-bg-color field" value="{{ $value -> order_price }}">
	 							</td>

                <td>
                  <span class="font-7"> {{ $value -> deliver ? 'მიწოდება ადგილზე' : 'ოფისიდან გატანა' }} </span>
                </td>

                <td>
                  <span> {{ $value -> created_at }} </span>
                </td>

								<td>
								 <select data-parameter-name="payment-method" class="wpr-100 record-update-data-list field">
									@foreach($data['paymentMethods'] as $paymentMethod)
									<option class="font-6" {{ $value -> payment_method_id == $paymentMethod -> id ? "selected" : null }} value="{{ $paymentMethod -> id }}">{{ $paymentMethod -> payment_method_title }}</option>
									@endforeach
								 </select>
                </td>

	 							<td>
	 							 <select data-parameter-name="order-status" class="wpr-100 record-update-data-list field">
	 								@foreach($data['orderStatuses'] as $orderStatus)
	 								 <option class="font-6" {{ $value -> order_status == $orderStatus -> order_status_name ? "selected" : null }} value="{{ $orderStatus -> order_status_name }}">{{ $orderStatus -> order_status_title }}</option>
	 								@endforeach
	 							 </select>
	 							</td>

	 							<td class="text-center">
	 							 <a class="view-page" href="{{ route('editCpanelOrder', ['shopOrderId' => $value -> id]) }}">
	 								<i style="font-size: 18px" class="fas fa-edit" aria-hidden="true"></i>
	 							 </a>
	 							</td>

	 							<td class="text-center">
	 								<a class="delete-record" href="{{ route('destroyCpanelOrder', ['shopOrderId' => $value -> id]) }}">
	 								 <i style="font-size: 16px" class="fas fa-trash-alt" aria-hidden="true"></i>
	 								</a>
	 							</td>

	 						</tr>

	           @endforeach

	 				 </tbody>
	 				</table>

	         <div data-page-key="{{ $data['paginationKey'] }}" data-current-page="{{ $data['paginator'] -> currentPage }}" class="clearfix pagination-column col-sm-12">

	           <div class="col-sm-3" style="padding-left: 0">
	             <div class="input-group">
	               <span class="input-group-addon" id="search-button">
	                 <i class="fas fa-search"></i>
	               </span>
	               <input id="search-field" type="text" autocomplete="off" data-control-parameter-name="search-query" value="{{ $data['selectedOrderId'] }}" class="form-control font-8 control-field" placeholder="შეკვეთის აიდი">
	             </div>
	    			  </div>

	           <select class="control-data-list col-sm-2 p-n mr20" data-control-parameter-name="{{ $data['orderStatusKey'] }}">
	             <option class="font-6" value="0">ყველა სტატუსის შეკვეთები</option>
	              @foreach($data['orderStatuses'] as $orderStatus)
	                <option value="{{ $orderStatus -> order_status_name }}" {{ $data['selectedOrderStatus'] == $orderStatus -> order_status_name ? "selected" : null }} class="font-6"> {{ $orderStatus -> order_status_title }} </option>
	              @endforeach
	           </select>

             <select class="control-data-list col-sm-2 p-n mr20" data-control-parameter-name="{{ $data['paymentMethodKey'] }}">
	             <option class="font-6" value="0">ყველა გადახდის მეთოდები</option>
	              @foreach($data['paymentMethods'] as $paymentMethod)
	               <option value="{{ $paymentMethod -> id }}" {{ $data['selectedPaymentMethod'] == $paymentMethod -> id ? "selected" : null }} class="font-6"> {{ $paymentMethod -> payment_method_title }} </option>
	              @endforeach
	           </select>

	           <ul class="pagination mt30 mb20 col-sm-12">
	               @if($data['paginator'] -> currentPage > 1)
	                <li>
	                 <a href="{{ $data['paginator'] -> currentPage - 1 }}" class="page-switch">
	                   <i class="fa fa-angle-double-left" aria-hidden="true"></i>
	                 </a>
	                </li>
	               @endif

	               @foreach($data['paginator'] -> pages as $page)

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

	               @if($data['paginator'] -> currentPage < $data['paginator'] -> maxPage)
	               <li>
	                 <a href="{{ $data['paginator'] -> currentPage + 1 }}" class="page-switch">
	                   <i class="fa fa-angle-double-right" aria-hidden="true"></i>
	                 </a>
	               </li>
	               @endif
	            </ul>

	         </div>
				 </div>
				</div>
			</div>
		</div>
  </div>
</div>

@include('parts.controlPanel.parameters')
@include('parts.controlPanel.lists')

<script type="text/javascript">

// view page control logic

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

// switches control logic

$(".form-switch").bootstrapSwitch(); // enable form switches

$(".form-switch").on("switchChange.bootstrapSwitch", function(event, state) { // handle form switch state change

		this.value = state ? 1 : 0;
});

$(".record-update-switch").bootstrapSwitch(); // enable record update switches

$(".record-update-switch").on("switchChange.bootstrapSwitch", function(event, state) { // handle record update switch

		 this.value = state ? 1 : 0;
		 fieldValueChangeHandler.call(this);
 });

</script>
