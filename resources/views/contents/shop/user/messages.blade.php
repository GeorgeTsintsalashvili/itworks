
@extends('layouts.shop')

@section('content')

<!-- breadcrumb start -->
<div class="breadcrumb">
 <div class="container">
  <div class="row">
   <div class="col-md-12">
    <a class="breadcrumb-part breadcrumb-link" href="/">
     <i class="fa fa-home"></i>
    </a>
    <span class="breadcrumb-part font-5">შეტყობინებები</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

@if(!$contentData['numOfMessages'])

<div class="container">
 <div class="row align-items-start">
  <div class="col-sm-12">
    <div class="messages-main-heading messages-empty">
     <span class="messages-heading-text">თქვენი არ გაქვთ შემოსული შეტყობინებები</span>
    </div>
  </div>
 </div>
 <!-- .row -->
</div>

@else

<!--- display messages --->

@endif

<style type="text/css">

.messages-main-heading.messages-empty {
  margin: 150px 0 150px 0;
}

.messages-heading-text {
  font-size: 25px;
  font-weight: 400;
  font-family: font-8;
}

.messages-main-heading {
  background: #f8f7f7 none repeat scroll 0 0;
  color: #333;
  font-size: 35px;
  font-weight: 300;
  padding: 40px 0;
  text-align: center;
  margin-bottom: 30px;
  border: 1px solid #e5e5e5;
}

</style>

@endsection
