@extends('layouts.shop')

@section('facebookShareImage', '/images/facebookShareImages/nawilebi.png')

@section('content')

@if($contentData['hardDiskDriveExists'])

<div id="fb-root"></div>

<!-- breadcrumb start -->
<div class="breadcrumb">
 <div class="container">
  <div class="row">
   <div class="col-md-12">
    <a class="breadcrumb-part breadcrumb-link" href="/">
     <i class="fa fa-home"></i>
    </a>
    <a class="breadcrumb-part breadcrumb-link" href="/hardDiskDrives">
     <span class="font-5"> HDD მეხსიერება</span>
    </a>
    <span class="breadcrumb-part font-5">{{ $contentData['hardDiskDrive'] -> title }}</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->


<!--- product details section --->

<section class="product-details-section">
 <div class="container">
  <div class="row">
   <div class="itw-normal-cols col-xs-12 col-sm-12">
    <div id="cart-notification" style="display: none">
     <span class="font-7 notification-text">
      <a href="#cart-notification" id="initial-text">პროდუქტი კოდით</a>
      <b class="product-code-placeholder"></b>
      <span class="main-phrase"></span>
      <a target="_blank" class="shopping-cart-link" href="{{ route('shoppingCart') }}"> კალათაში</a>
     </span>
     <span id="cart-notification-close">
      <i class="fa fa-times"></i>
     </span>
    </div>
   </div>
  </div>

  <div class="row">
   <div class="itw-normal-cols col-xs-12 col-sm-12">
    <div class="itw-normal-cols">
     <div class="primary-block row itw-media-bottom">
      <!-- left infos-->
      <div class="pb-left-column col-md-5 flex-column">
       <div class="image-block-wrapper position-relative">
        <!-- product img-->
        <div id="image-block" class="clearfix">
         <span id="view-full-size">
          <span class="border-product"></span>
          <img id="big-image" src="/images/hardDiskDrives/main/original/{{ $contentData['hardDiskDrive'] -> mainImage }}" width="800" height="850" />
          <span class="span-link no-print">
           <span class="hidden"></span>
          </span>
         </span>
        </div>
       </div>
       <!-- end image-block -->

       <!-- additional images slider -->
       <div id="product-images-slider" class="splide">
       	<div class="splide__track">
       		<ul class="splide__list">
            <li class="splide__slide">
             <a href="/images/hardDiskDrives/main/original/{{ $contentData['hardDiskDrive'] -> mainImage }}" data-fancybox="other-views" class="fancybox shown">
              <img class="img-fluid" src="/images/hardDiskDrives/main/preview/{{ $contentData['hardDiskDrive'] -> mainImage }}" />
             </a>
            </li>
            @if($contentData['imagesExist'])
            @foreach($contentData['images'] as $value)
            <li class="splide__slide">
             <a href="/images/hardDiskDrives/slides/original/{{ $value -> image }}" data-fancybox="other-views" class="fancybox">
              <img class="img-fluid" src="/images/hardDiskDrives/slides/preview/{{ $value -> image }}"/>
             </a>
            </li>
            @endforeach
            @endif
       		</ul>
       	</div>
       </div>
      </div>
      <!-- end pb-left-column -->
      <!-- end left infos-->
      <!-- center infos -->

      <div class="col-md-7 col-sm-12 col-xs-12 info">
       <div class="product-details-title-wrapper">
        <div class="product-details-title">
         <h1 class="font-4">{{ $contentData['hardDiskDrive'] -> title }}</h1>
        </div>
       </div>

       <!--- price details start --->
       <div class="prices-container">
        <p class="current-price-wrapper no-margin">
         <span id="current-price">₾ {{ $contentData['hardDiskDrive'] -> newPrice }} </span>
        </p>
        @if($contentData['hardDiskDrive'] -> discount != 0)
        <p class="old-price-wrapper no-margin">
         <span id="old-price"> ₾ {{ $contentData['hardDiskDrive'] -> price }} </span>
        </p>
        @endif
       </div>
       <!--- price details end --->

       <p id="product-warranty">
        <i class="fas fa-shield-check"></i>
        <span class="font-8">მოქმედებს {{ $contentData['hardDiskDrive'] -> warrantyDuration }} {{ $contentData['warrantyTitle'] }} გარანტია საუკეთესო პირობებით</span>
       </p>

       <p id="installment-allowed">
        <i class="fas fa-calendar-alt"></i>
        <span class="font-8">შეგიძლიათ ისარგებლოთ განვადებით</span>
       </p>

       <p id="stock-status">
        <a class="stock-status-title" style="background: {{ $contentData['stockStatusColor'] }}">
         <span> {{ $contentData['stockTitle'] }}</span>
        </a>
       </p>

       <p id="product-condition">
        <b class="font-6">მდგომარეობა </b>
        <span>{{ $contentData['conditionTitle'] }}</span>
       </p>

       <p id="product-code">
        <b class="font-6">პროდუქტის კოდი </b>
        <span>{{ $contentData['hardDiskDrive'] -> categoryId }}-{{ $contentData['hardDiskDrive'] -> id }}</span>
       </p>

       @if($contentData['hardDiskDrive'] -> quantity != 0)
       <p id="product-quantity">
        <b class="font-6">საერთო რაოდენობა </b>
        <span>{{ $contentData['hardDiskDrive'] -> quantity }}</span>
       </p>
       @endif

       <!--- Share buttons --->

       <div class="social-networks-wrapper">
        <div class="fb-share-button" data-href="/hardDiskDrives/{{ $contentData['hardDiskDrive'] -> id }}" data-size="large" data-layout="button_count" style="position: absolute;"></div>
       </div>

       <!--- Add to cart form start --->

       @if($contentData['enableAddToCartButton'])

       <form id="add-to-cart-form" action="/shop/cart/add" method="post" data-allowed-quantity="{{ $contentData['hardDiskDrive'] -> quantity }}">
        <div class="box-info-product">
         <div class="list-options-cart clearfix">
          <div class="list-options-cart-inner clearfix">
           <div class="quantity-product-option">
            <div id="quantity-wanted-wrapper">
             <div class="quantity-value-option">
              <div class="qty-button-control itw-quantity-minus">
               <a href="#" data-field-quantity="quantity" class="btn btn-secondary button-minus product-quantity-down">
                <span>
                 <i class="fas fa-minus"></i>
                </span>
               </a>
              </div>
              <input type="text" name="quantity" value="1" id="quantity-wanted" min="1" class="text" />
              <input type="hidden" name="product-id" value="{{ $contentData['hardDiskDrive'] -> id }}" />
              <input type="hidden" name="category-id" value="{{ $contentData['hardDiskDrive'] -> categoryId }}" />
              <div class="qty-button-control itw-quantity-plus">
               <a href="#" data-field-quantity="quantity" class="btn btn-secondary button-plus product-quantity-up">
                <span>
                 <i class="fas fa-plus"></i>
                </span>
               </a>
              </div>
             </div>
            </div>
           </div>
          </div>
         </div>

         <div class="box-cart-info-wapper">
          <div class="box-cart-bottom float-left">
           <div class="cart-info-add">
            <p id="add-to-cart-button-wrapper" class="buttons-bottom-block">
             <button type="submit" class="add-to-cart-button">
              <span>
               <i class="fa fa-cart-plus"></i>
               <b class="add-to-cart-label font-5">კალათაში დამატება</b>
              </span>
             </button>
            </p>
           </div>
          </div>
         </div>
        </div>
        <!-- end box-info-product -->
       </form>
       @endif
       <!--- Add to cart form end------------>
      </div>
      <!-- end center infos-->
     </div>
     <!-- end primary-block -->
    </div>
    <!-- itemscope product wrapper -->
   </div>
   <!-- #-->
  </div>
  <!-- .row -->

  <!--- tabs control start --->
  <div class="row">
    <div class="col-lg-12">
      <div class="tabs-block">
       <ul class="nav nav-tabs" id="itw-tab" role="tablist">
        <li class="nav-item">
         <a class="nav-link active" id="description-tab" data-bs-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">მახასიათებლები</a>
        </li>
        <li class="nav-item">
         <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments" role="tab" aria-controls="comments" aria-selected="false">გამოხმაურება</a>
        </li>
       </ul>
       <div class="tab-content" id="tab-content">
        <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">{!! $contentData['hardDiskDrive'] -> description !!}</div>
        <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
         <div data-width="500" class="fb-comments" data-href="{{ url() -> current() }}" data-numposts="5"></div>
        </div>
       </div>
      </div>
    </div>
  </div>
  <!--- tabs control end --->
 </div>
 <!-- .container -->
</section>

<!--- Section for same products carousel --->

@if($contentData['recommendedHardDiskDrivesExist'])

<section class="itw-products-section blockPosition itw-bg-gray">
 <div class="container">
  <div class="row">
   <div class="col-lg-12">
    <div class="standard-products-carousel-block clearfix show-hover2">
     <div class="standard-products-carousel-data">
      <div class="fancy-heading text-left top-list-title">
       <h3>
        <span class="font-6"> მსგავსი პროდუქცია</span>
       </h3>
      </div>
      <div class="itw-top-product-category-inner">
       <div class="itw-products-wrapper">
        <div class="protab-contents">
         <div class="itw-products-data col-lg-12 col-sm-12 col-xs-12 position-relative">
          <div class="itw-top-product-inner-category">
            <div class="splide__arrows">
             <button class="splide__arrow splide__arrow--prev">
              <i class="fas fa-angle-left"></i>
             </button>
             <button class="splide__arrow splide__arrow--next">
              <i class="fas fa-angle-right"></i>
             </button>
            </div>
           <div class="product-wrapper splide__track">
            <!-- Products list -->
            <ul class="product-list itw-product-top splide__list">
             @foreach($contentData['recommendedHardDiskDrives'] as $value)

             <li class="ajax-block-product col-sm-3 position-relative splide__slide">
              <div class="product-container">
               <div class="itw-display-product-info">
                <div class="left-block">
                 <div class="product-image-container">
                  <a href="/hardDiskDrives/{{ $value -> id }}">
                   <img class="replace-2x img-fluid" src="/images/hardDiskDrives/main/original/{{ $value -> mainImage }}" />
                  </a>
                 </div>
                </div>

                <div class="right-block">
                 <h5>
                  <a class="product-name font-7" href="/hardDiskDrives/{{ $value -> id }}">
                   <span class="font-5">{{ $value -> title }}</span>
                  </a>
                 </h5>

                 <div class="content-price">
                  @if($value -> discount != 0)

                  <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> newPrice }}</span>
                  <span class="old-price product-price">₾ {{ $value -> price }}</span>

                  @else
                  <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> price }}</span>
                  @endif
                 </div>
                </div>
               </div>

               <div class="product-more-options">
                <div class="read-more-container">
                 <a class="button read-more-button btn btn-secondary" href="/hardDiskDrives/{{ $value -> id }}">
                  <span class="font-5">დაწვრილებით</span>
                 </a>
                </div>
                <div class="short-description">
                 <div>
                  <b class="font-6">სიჩქარე: </b>
                  <span>{{ $value -> rpm }}</span>
                  <span class="font-5"> ბრ/წთ</span>
                 </div>
                 <div>
                  <b class="font-6">მოცულობა: </b>
                  <span>{{ $value -> capacity }} GB</span>
                 </div>
                 <div>
                  <b class="font-6">ფორმფაქტორი: </b>
                  <span>{{ $value -> formFactorTitle }}</span>
                 </div>
                </div>
               </div>
              </div>
              <!-- .product-container> -->
             </li>
             @endforeach
            </ul>
           </div>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>

@endif

@include('parts.shop.view')

@endif

@endsection
