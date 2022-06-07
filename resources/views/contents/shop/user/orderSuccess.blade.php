
@extends('layouts.shop')

@section('title', 'შეკვეთის წარმატებულად განთავსება')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-12">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">შეკვეთის მონაცემები</h2>
     <form autocomplete="off">
      <div class="mb-4">
       <div class="form-description">
         <span>თქვენ წარმატებით განათავსეთ შეკვეთა აიდით <b>#{{ $contentData['order'] -> id }}</b>. გთხოვთ დაელოდოთ მაღაზიისგან შეკვეთის დადასტურებას, რის შემდეგაც მოგეცემათ ანგარიშსწორების საშუალება. </span>
       </div>
      </div>
      <div class="form-row mb-4">
        <div class="order-summary">
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>შემკვეთის სახელი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> customer_name }}</span>
            </div>
          </div>
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>შემკვეთის ტელეფონი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> customer_phone }}</span>
            </div>
          </div>
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>შეკვეთის ფასი</b>
            </div>
            <div class="order-info-value">
              <span>₾ {{ $contentData['order'] -> order_price }}</span>
            </div>
          </div>
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>პროდუქციის რაოდენობა</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> total_quantity }} ერთეული</span>
            </div>
          </div>
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>მიწოდების მეთოდი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> deliver ? "ადგილზე მიწოდება" : "მაღაზიიდან გატანა" }}</span>
            </div>
          </div>
          @if($contentData['order'] -> delivery_address)
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>მიწოდების მისამართი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> delivery_address }}</span>
            </div>
          </div>
          @endif
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>გადახდის მეთოდი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> payment_method_title }}</span>
            </div>
          </div>
          <div class="order-summary-item">
            <div class="order-info-key">
              <b>განთავსების თარიღი</b>
            </div>
            <div class="order-info-value">
              <span>{{ $contentData['order'] -> order_placement_date }}</span>
            </div>
          </div>
        </div>
        <div class="order-products-list">
          <h5 class="selected-products-title">არჩეული პროდუქცია </h5>
          @foreach($contentData['order'] -> order_items as $orderItem)
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
        <div class="mt-4">
          <button type="submit" class="btn btn-primary shadow-none font-5" onclick="window.print()" id="order-print-button">
            <i class="fas fa-print"></i>
            <span> შეკვეთის ამობეჭდვა </span>
          </button>
          <a class="btn btn-secondary shadow-none font-6" href="{{ route('shopUserOrders') }}" id="orders-page-link">
            <i class="fas fa-globe"></i>
            <span>შეკვეთების გვერდზე გადასვლა</span>
          </a>
        </div>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

<style type="text/css">

.order-summary-item {
  display: flex;
  justify-content: space-between;
}

.order-summary-item b {
  margin-right: 10px;
  font-size: 16px;
  font-family: 'font-8';
}

.order-summary-item span {
  font-size: 16px;
  font-family: 'font-7';
}

.order-summary-item:not(:last-child) {
  border-bottom: 1px solid #d6d6d6;
}

.order-summary-item {
  padding: 14px;
}

.order-summary {
  border: 1px solid #d6d6d6;
  background-color: #f6f6f6;
}

#order-print-button {
  margin-right: 10px;
}

#orders-page-link {
  background-color: #595959;
  border: 1px solid #595959;
  color: #fff;
}

#orders-page-link:hover {
  background-color: #fff;
  color: #595959;
}

@media print {
   .general-form-page-delimiter,
   #header,
   #footer,
   #itw-topbar,
   #order-print-button,
   #orders-page-link {
     display: none;
   }
}

.order-products-list {
  margin-top: 30px;
}

.order-products-list .selected-products-title {
  font-size: 24px;
  font-family: font-6;
  margin-bottom: 30px;
}

.order-item-row:first-child {
  margin-top: 20px;
}

.order-item-row {
  padding: 12px;
  display: flex;
  justify-content: space-between;
  border: 1px solid #d4d4d4;
  background-color: #fafafa;
  margin-bottom: 20px;
}

.order-item-col {
  width: 20%;
}

.order-item-col:not(:first-child) {
  text-align: center;
}

.order-item-col:not(:last-child) {
  margin-right: 30px;
}

.order-item-row img {
  background-color: #fff;
  border: 1px solid #d4d4d4;
  padding: 10px;
  width: 150px;
}

.order-item-col-title {
  font-size: 16px;
  font-family: font-8;
  font-weight: bold;
  color: #595959;
  margin-bottom: 20px;
}

.order-item-col-info {
  font-family: font-7;
  font-size: 16px;
}

.order-item-col-info a {
  text-decoration: none;
  color: #4f4f4f;
}

.order-item-col-info a:hover {
  text-decoration: underline;
  color: #0a756a;
}

@media (max-width: 768px) {

  .order-item-row img {
    width: 100%;
  }

  .order-item-col:not(:last-child),
  .order-item-col:last-child {
    margin: 20px auto;
  }

  .order-item-row {
    flex-direction: column;
  }

  .order-item-col {
    width: 60%;
    text-align: center;
  }
}

#orders-page-link,
#order-print-button {
  margin-bottom: 20px;
}

.order-info-key {
  margin-right: 10px;
  font-size: 14px;
  font-family: font-8;
  width: 50%;
}

.order-info-value {
  font-size: 14px;
  font-family: font-7;
  width: 50%;
  text-align: right;
}

</style>

@endsection
