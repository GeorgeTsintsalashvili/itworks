
@extends('layouts.shop')

@section('title', 'წარუმატებული გადახდა')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

@if(isset($jsonData))

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-9">
    <div class="general-form">
     <div class="payment-status error">
      <div class="payment-title">
        <i class="far fa-exclamation-circle"></i>
        <div class="payment-text">გადახდის შეცდომა</div>
      </div>
     </div>
     <form autocomplete="off">
      <div class="mb-4">
       <div class="form-description">
         <span>შეკვეთის აიდით <b>{{ $jsonData -> purchaseUnit -> shop_order_id }}</b> ღირებულების გადახდა ვერ შესრულდა. </span>
       </div>
      </div>
      <h2 class="font-6 mb-4 form-heading">ტრანზაქციის დეტალები</h2>
      <div class="form-row">
        <div class="summary-row">
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>ოპერაციის შესრულების დრო</b>
            </div>
            <div class="row-info-value">
              <span>Custom Text</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>გადასახდელი თანხა</b>
            </div>
            <div class="row-info-value">
              <span>Custom Text</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>ოპერაციის აიდი</b>
            </div>
            <div class="row-info-value">
             <span>Custom Text</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
             <b>სტატუსი</b>
            </div>
            <div class="row-info-value">
              <span>Custom Text</span>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <a class="btn shadow-none font-5 page-link-button" href="{{ route('shopUserOrders') }}">
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
@endif

<style type="text/css">

.payment-status {
  margin-bottom: 40px;
  text-align: center;
  margin-top: 20px;
}

.payment-status i {
  font-size: 60px;
  margin-bottom: 20px;
}

.payment-status .payment-text {
  font-family: font-5;
  font-size: 24px;
}

.payment-status.error i,
.payment-status.error .payment-text {
  color: #f91d1d;
}

.summary-row-item {
  display: flex;
  justify-content: space-between;
}

.summary-row-item b {
  margin-right: 10px;
  font-size: 16px;
  font-family: 'font-8';
}

.summary-row-item span {
  font-size: 16px;
  font-family: 'font-7';
}

.summary-row-item:not(:last-child) {
  border-bottom: 1px solid #d6d6d6;
}

.summary-row-item {
  padding: 14px;
}

.summary-row {
  border: 1px solid #d6d6d6;
  background-color: #f6f6f6;
}

.standard-button {
  margin-right: 10px;
}

.page-link-button {
  background-color: #595959;
  border: 1px solid #595959;
  color: #fff;
  border-radius: 0;
}

.page-link-button:hover {
  background-color: #fff;
  color: #595959;
}

@media print {
   .general-form-page-delimiter,
   #header,
   #footer,
   #itw-topbar,
   .standard-button,
   .page-link-button {
     display: none;
   }
}

.page-link-button,
.standard-button {
  margin-bottom: 20px;
}

.row-info-key {
  margin-right: 10px;
  font-size: 14px;
  font-family: font-8;
  width: 50%;
}

.row-info-value {
  font-size: 14px;
  font-family: font-7;
  width: 50%;
  text-align: right;
}

</style>

@endsection
