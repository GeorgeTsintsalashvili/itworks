
@if($data['shopOrder'])

<div class="panel panel-default" id="product-data-container" data-record-id="{{ $data['shopOrder'] -> id }}">

 <div class="panel-heading">
   <h2 id="back-button">
     <i class="fas fa-long-arrow-alt-left"></i>
     <span class="font-3"> უკან დაბრუნება </span>
   </h2>
   <div class="options">
    <ul class="nav nav-tabs">
      <li class="active">
        <a href="#editor" data-toggle="tab" class="font-6">შეკვეთა</a>
      </li>
      <li>
        <a href="#order-products" data-toggle="tab" class="font-6">პროდუქცია</a>
      </li>
    </ul>
   </div>
 </div>

 <div class="panel-body">
  <div class="tab-content">
    <div class="tab-pane active" id="editor">
      <form class="form-horizontal record-update-form" method="POST" action="{{ route('updateCpanelOrder', ['shopOrderId' => $data['shopOrder'] -> id]) }}">

          <div class="form-group">
            <div class="col-sm-4">
             <label class="control-label font-6">შემკვეთი</label>
             <input type="text" disabled class="form-control font-7" value="{{ $data['shopOrder'] -> customer_name }}">
            </div>

            <div class="col-sm-4">
              <label class="control-label font-6">ფოსტა</label>
             <input type="text" disabled class="form-control font-7" value="{{ $data['shopUser'] -> email }}">
            </div>

             <div class="col-sm-4">
              <label class="control-label font-6">ტელეფონი</label>
              <input type="text" disabled class="form-control font-7" value="{{ $data['shopOrder'] -> customer_phone }}">
             </div>

           </div>

           <div class="form-group">

             <div class="col-sm-4">
              <label class="control-label font-6">განთავსების დრო</label>
              <input type="text" disabled class="form-control font-7" value="{{ $data['shopOrder'] -> created_at }}">
             </div>

             <div class="col-sm-4">
              <label class="control-label font-6">გადახდის დრო</label>
              <input type="text" readonly class="form-control font-7" value="{{ $data['shopOrder'] -> paid_at }}">
             </div>

             <div class="col-sm-4">
              <label class="control-label font-6">მიწოდების მეთოდი</label>
              <input type="text" disabled class="form-control font-7" value="{{ $data['shopOrder'] -> deliver ? 'ადგილზე მიწოდება' : 'გატანა ოფისიდან' }}">
             </div>

           </div>

           <div class="form-group">

             <div class="col-sm-4">
              <label class="control-label font-6">ბანკის შეკვეთის აიდი</label>
              <input type="text" readonly class="form-control font-7" value="{{ $data['shopOrder'] -> bank_order_id }}">
             </div>

             <div class="col-sm-4">
              <label class="control-label font-6">მაღაზიის შეკვეთის აიდი</label>
              <input type="text" readonly class="form-control font-7" value="{{ $data['shopOrder'] -> id }}">
             </div>

             <div class="col-sm-4">
              <label class="control-label font-6">მიწოდების მისამართი</label>
              <input type="text" readonly class="form-control font-7" value="{{ $data['shopOrder'] -> delivery_address }}">
             </div>

           </div>

           <div class="form-group">

             <div class="col-sm-4">
              <label class="control-label font-6">შეკვეთის სტატუსი</label>
              <select name="order-status" class="edit-page-list wpr-100">
                @foreach($data['orderStatuses'] as $orderStatusItem)
                  @if($orderStatusItem -> order_status_name == $data['shopOrder'] -> order_status)
                   <option selected class="font-6" value="{{ $orderStatusItem -> order_status_name }}">{{ $orderStatusItem -> order_status_title }}</option>
                  @else
                   <option class="font-6" value="{{ $orderStatusItem -> order_status_name }}">{{ $orderStatusItem -> order_status_title }}</option>
                  @endif
                @endforeach
              </select>
             </div>

             <div class="col-sm-4">
               <label class="control-label font-6">გადახდის მეთოდი</label>
               <select name="payment-method" class="edit-page-list wpr-100">
                 @foreach($data['paymentMethods'] as $paymentMethodItem)
                   @if($paymentMethodItem -> id == $data['shopOrder'] -> payment_method_id)
                    <option selected class="font-6" value="{{ $paymentMethodItem -> id }}">{{ $paymentMethodItem -> payment_method_title }}</option>
                   @else
                    <option class="font-6" value="{{ $paymentMethodItem -> id }}">{{ $paymentMethodItem -> payment_method_title }}</option>
                   @endif
                 @endforeach
               </select>
             </div>

           </div>

           <div class="form-group">
             <div class="col-sm-4">
              <label class="control-label font-6">შეკვეთის ღირებულება</label>
              <input type="text" name="order-price" class="form-control font-7" value="{{ $data['shopOrder'] -> order_price }}">
             </div>
             <div class="col-sm-4">
              <label class="control-label font-6">საერთო რაოდენობა</label>
              <input type="text" readonly class="form-control font-7" value="{{ $data['shopOrder'] -> total_quantity }}">
             </div>
           </div>

           <div class="panel-footer">
            <div class="row">
              <div class="col-sm-2">
                <input type="submit" class="btn-primary btn font-6" value="მონაცემების განახლება">
              </div>
              @if($data['paymentProvider'] == 'bog' && $data['shopOrder'] -> bank_order_id)
              <div class="col-sm-3">
               <a class="btn-primary btn font-6" href="{{ route($data['paymentMethod'] -> id, ['bankOrderId' => $data['shopOrder'] -> bank_order_id]) }}" target="_blank">სტატუსის შემოწმება</a>
              </div>
              @endif
            </div>
          </div>

       </form>
    </div>

    <div class="tab-pane" id="order-products">
     <div class="order-products-container">
       @foreach($data['shopOrderItems'] as $orderItem)
       <div class="order-item">
         <div class="row mb40">
           <div class="order-image-col col-sm-2">
            <a href="{{ $orderItem -> product_route }}" target="_blank">
             <img src="{{ $orderItem -> product_image }}" class="order-item-image">
            </a>
           </div>
           <div class="order-col col-sm-3">
            <div class="order-col-head">დასახელება</div>
            <div class="order-col-body">{{ $orderItem -> product_title }}</div>
           </div>
           <div class="order-col col-sm-3">
            <div class="order-col-head order-text-center">ღირებულება</div>
            <div class="order-col-body order-text-center">₾ {{ $orderItem -> order_item_price }}</div>
           </div>
           <div class="order-col col-sm-2">
            <div class="order-col-head order-text-center">რაოდენობა</div>
            <div class="order-col-body order-text-center">{{ $orderItem -> order_item_quantity }}</div>
           </div>
           <div class="order-col col-sm-2">
            <div class="order-col-head order-text-center">კოდი</div>
            <div class="order-col-body order-text-center">{{ $orderItem -> product_category_id }}-{{ $orderItem -> product_id }}</div>
           </div>
         </div>
       </div>
       @endforeach
     </div>
    </div>

  </div>
 </div>
</div>

@include('parts.controlPanel.general')

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

// intialize lists

$(".edit-page-list").select2();

// form submit handler

function updateRecord(event)
{
  event.preventDefault();

  let pageAddress = $(this).attr("action");
  let method = $(this).attr("method");
  let formData = $(this).serialize();
  let updateSuccess = (response) => printMessage(response["updated"] ? "განახლება შესრულდა" : "განახლება არ შესრულდა");

  sendRequest(method, pageAddress, formData, updateSuccess);
}

// bind handler

$(".record-update-form").submit(updateRecord);

</script>

@endif
