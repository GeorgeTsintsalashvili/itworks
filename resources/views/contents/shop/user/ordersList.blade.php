
@foreach($orders as $order)
<div class="order-item" data-order-id="{{ $order -> id }}">
 <div class="order-row">
  <div class="order-info-key">
   <b>შეკვეთის აიდი</b>
  </div>
  <div class="order-info-value">
   <span class="order-id-info">{{ $order -> id }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>შემკვეთის სახელი და გვარი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> customer_name }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>შემკვეთის ტელეფონი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> customer_phone }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>განთავსების თარიღი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> order_placement_date }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>შეკვეთის ღირებულება</b>
  </div>
  <div class="order-info-value">
   <span>₾ {{ $order -> order_price }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>მიწოდების მეთოდი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> deliver ? "ადგილზე მიწოდება" : "მაღაზიიდან გატანა" }}</span>
  </div>
 </div>
 @if($order -> delivery_address)
 <div class="order-row">
  <div class="order-info-key">
   <b>მიწოდების მისამართი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> delivery_address }}</span>
  </div>
 </div>
 @endif
 <div class="order-row">
  <div class="order-info-key">
   <b>გადახდის მეთოდი</b>
  </div>
  <div class="order-info-value">
   <span>{{ $order -> payment_method_title }}</span>
  </div>
 </div>
 <div class="order-row">
  <div class="order-info-key">
   <b>შეკვეთის სტატუსი</b>
  </div>
  <div class="order-info-value">
   <span style="color: {{ $order -> order_status_color }}">{{ $order -> order_status_title }}</span>
  </div>
 </div>
 @if($order -> order_status_name == 'confirmed')
 <div class="order-row payment-deadline-row" data-payment-deadline="{{ $order -> payment_deadline }}" style="display: none">
  <div class="order-info-key">
   <b>გადახდამდე დარჩენილი დრო</b>
  </div>
  <div class="order-info-value">
   <span></span>
  </div>
 </div>
 @endif
 <div class="order-item-buttons-row">
  <button class="btn btn-orange shadow-none order-print-button">
    <i class="fas fa-print"></i>
    <span>შეკვეთის ამობეჭდვა</span>
  </button>
  <button class="btn btn-standard shadow-none products-visibility-control-button">
    <i class="far fa-eye"></i>
    <span>პროდუქციის ნახვა</span>
  </button>
  <button class="btn btn-standard shadow-none order-id-copy-button">
    <i class="far fa-copy"></i>
    <span>აიდის დაკოპირება</span>
  </button>
  @if($order -> order_status_name == 'confirmed' && $order -> payment_method_class == 'card')
  <a class="btn btn-green shadow-none order-pay-button" href="/shop/purchaseOrder/{{ $order -> id }}">
    <span>ბარათით გადახდა</span>
  </a>
  @elseif($order -> order_status_name == 'confirmed' && $order -> payment_method_class == 'installment' && $order -> payment_method_provider == 'bog')
  <div class="btn bog-smart-button" data-order-price="{{ $order -> order_price }}">განვადების მოთხოვნა</div>
  @elseif($order -> order_status_name == 'pending')
  <button class="order-cancel-button btn btn-standard shadow-none">
   <i class="fas fa-ban"></i>
   <span> შეკვეთის გაუქმება </span>
  </button>
  @endif
  @if($order -> allow_delete)
  <button class="btn btn-red shadow-none order-delete-button">
    <i class="far fa-trash"></i>
    <span>შეკვეთის წაშლა</span>
  </button>
  @endif
 </div>
 <div class="order-products-list" data-product-list-visibility="0" style="display: none">
   @foreach($order -> order_items as $orderItem)
   <div class="order-item-row">
     <div class="order-item-col">
      <a href="{{ $orderItem -> product_route }}" target="_blank">
       <img src="{{ $orderItem -> product_image }}">
      </a>
     </div>
     <div class="order-item-col">
      <div class="order-item-col-title">დასახელება</div>
      <div class="order-item-col-info">
       <a href="{{ $orderItem -> product_route }}" target="_blank">{{ $orderItem -> product_title }}</a>
      </div>
     </div>
     <div class="order-item-col">
      <div class="order-item-col-title">კოდი</div>
      <div class="order-item-col-info">
       <span>{{ $orderItem -> product_category_id }}-{{ $orderItem -> product_id }}</span>
      </div>
     </div>
     <div class="order-item-col">
      <div class="order-item-col-title">რაოდენობა</div>
      <div class="order-item-col-info">
       <span>{{ $orderItem -> order_item_quantity }} ერთეული</span>
      </div>
     </div>
     <div class="order-item-col">
      <div class="order-item-col-title">ფასი</div>
      <div class="order-item-col-info">
       <span>₾ {{ $orderItem -> order_item_price }}</span>
      </div>
     </div>
   </div>
   @endforeach
 </div>
</div>
@endforeach
