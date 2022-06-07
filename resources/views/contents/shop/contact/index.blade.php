
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
    <span class="breadcrumb-part font-5">კონტაქტი</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

<section id="contact-form" class="mb-5">
 <div class="container">

	<!--- notification part --->
  <div class="row">
   <div class="itw-normal-cols col-sm-12">
    <div id="contact-notification" class="mb-5" style="display: none">
     <span class="font-7 notification-text">
      <a id="notification" href="#notification" class="main-phrase"></a>
     </span>
     <span id="close">
      <i class="fa fa-times"></i>
     </span>
    </div>
   </div>
  </div>

  <!--- form and map part --->
  <div class="row">
   <div class="col-md-5">
    <h2 class="font-6">მოგვწერეთ</h2>
    <form action="/contact/sendMessage" method="POST" class="contact-form-box">

     @if(Auth::guard('shopUser')->check())

     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="name" value="{{ Auth::guard('shopUser')->user()->name }}" placeholder="სახელი და გვარი" />
      </div>
     </div>

     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="email" value="{{ Auth::guard('shopUser')->user()->email }}" placeholder="ელ. ფოსტის მისამართი" />
      </div>
     </div>

     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="phone" value="{{ Auth::guard('shopUser')->user()->phone }}" placeholder="მობილური ტელეფონის ნომერი" />
      </div>
     </div>

     @else

     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="name" placeholder="სახელი და გვარი" />
      </div>
     </div>
     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="email" value="" placeholder="ელ. ფოსტის მისამართი" />
      </div>
     </div>

     <div class="form-group-input row">
      <div class="form-group col-lg-12 col-sm-12 col-xs-12">
       <input class="form-control grey font-7" type="text" name="phone" placeholder="მობილური ტელეფონის ნომერი" />
      </div>
     </div>

     @endif

     <div class="form-group-area">
      <div class="form-group">
       <textarea class="form-control font-7" rows="8" name="message" placeholder="აკრიფეთ ტექსტი"></textarea>
      </div>
     </div>

     <div class="submit">
      <button type="submit" name="submitMessage" id="submitMessage" class="button btn btn-secondary button-medium">
       <b class="font-2">შეტყობინების გაგზავნა</b>
      </button>
     </div>
    </form>
   </div>

   <div class="col-md-7">
    <iframe width="100%" height="400px" src="{!! $generalData['contact'] -> googleMapLink !!}" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
    <input readonly id="address-link" value="{!! $generalData['contact'] -> googleMapLink !!}" />
    <a id="address-copy-button">
     <span class="font-5"> მისამართის ბმულის დაკოპირება </span>
    </a>
   </div>
  </div>
 </div>
</section>

<link rel="stylesheet" href="/css/contact.css?v=35" type="text/css" media="all" />
<script type="text/javascript" src="/js/contact.js?v=35"></script>

@endsection
