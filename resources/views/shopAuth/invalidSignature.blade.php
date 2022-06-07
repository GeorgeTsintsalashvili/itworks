
@extends('layouts.shop')

@section('title', 'ვერიფიკაციის შეცდომა')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">ვერიფიკაციის შეცდომა</h2>
     <form autocomplete="off">
      <div class="mb-4">
       <div class="form-description">
         <span>
          თქვენი იმეილის <b>{{ Auth::guard('shopUser') -> user() -> email }}</b> ვერიფიკაციის ლინკს მოქმედების ვადა ამოეწურა, ან მოხდა ლინკის პარამეტრების ხელით შეცვლა,
          რის გამოც ვერ მოხერხდა ელექტორნული ფოსტის მისამართის სინამდვილის დადასტურება. ახალი ლინკის გასაგზავნად საჭიროა გადახვიდეთ ამ <a href="{{ route('shop.verification.notice') }}">გვერდზე</a>.
          გაითვალისწინეთ, რომ ვერიფიკაციის ლინკის მოქმედების ვადა შეადგენს 60 წუთს.
         </span>
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

@endsection
