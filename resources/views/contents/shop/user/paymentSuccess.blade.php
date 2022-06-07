
@extends('layouts.shop')

@section('title', 'წარმატებული გადახდა')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-9">
    <div class="general-form">
     <div class="payment-status success">
      <div class="payment-title">
        <i class="far fa-check-circle"></i>
        <div class="payment-text">წარმატებული გადახდა</div>
      </div>
     </div>
     <form autocomplete="off">
      <div class="mb-4">
       <div class="form-description">
         <span>თქვენი შეკვეთის აიდით <b>345891271024</b> ღირებულება გადახდილია, გთხოვთ დაელოდოთ დაკავშირებას. </span>
       </div>
      </div>
      <h2 class="font-6 mb-4 form-heading">გადახდის მონაცემები</h2>
      <div class="form-row">
        <div class="summary-row">
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>გადამხდელი</b>
            </div>
            <div class="row-info-value">
              <span>{{ $contentData['user'] -> name }}</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>ტელეფონი</b>
            </div>
            <div class="row-info-value">
              <span>{{ $contentData['user'] -> phone }}</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>ელ. ფოსტა</b>
            </div>
            <div class="row-info-value">
             <span>{{ $contentData['user'] -> email }}</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>სულ გადახდილია</b>
            </div>
            <div class="row-info-value">
              <span>₾ 2000</span>
            </div>
          </div>
          <div class="summary-row-item">
            <div class="row-info-key">
              <b>გადახდის დრო</b>
            </div>
            <div class="row-info-value">
              <span>26-05-2022 15:24:07</span>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn btn-primary shadow-none font-5 standard-button" onclick="window.print()">
            <i class="fas fa-print"></i>
            <span> მონაცემების ამობეჭდვა </span>
          </button>
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

.payment-status.success i,
.payment-status.success .payment-text {
  color: #0b9f0f;
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
