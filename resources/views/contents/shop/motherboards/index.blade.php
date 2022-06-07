
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
    <span class="breadcrumb-part font-5">დედაპლატები</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

@if($contentData['motherboardsExist'])
<div class="container">
 <!--- products row --->
 <div class="row align-items-start">
  <div class="col-sm-3 filter">
   <div id="products-filter-block">
    <div class="block_content filter-item">
     <form action="{{ route('mbLoad') }}" class="filter-form" method="post">
      <div class="filter-form-container">
       <!--- active page parameter --->

       <input name="active-page" type="hidden" class="active-page" value="{{ $contentData['currentPage'] }}" />

       <!--- Price filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ღირებულება ლარებში</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         <div class="input-range price-range" data-min-price="{{ $contentData['configuration']['productPriceRange'] -> motherboardMinPrice }}" data-max-price="{{ $contentData['configuration']['productPriceRange'] -> motherboardMaxPrice }}">
          <input name="price-from" type="text" class="price-from" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['productPriceRange'] -> motherboardMinPrice }}" />
          <input name="price-to" type="text" class="price-to" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['productPriceRange'] -> motherboardMaxPrice }}" />
         </div>
         <div class="range-slider-container"></div>
        </ul>
       </div>
       <!--- Price filter end --->

       <!--- Manufacturer filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">მწარმოებელი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['motherboardsManufacturers'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="motherboard-manufacturer">
           <b>{{ $value -> manufacturerTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="motherboard-manufacturer" type="hidden" class="motherboard-manufacturer" value="0" />
       </div>

       <!--- Manufacturer filter end --->

       <!--- Form factor filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ფორმფაქტორი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['formFactors'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="form-factor">
           <b>{{ $value -> formFactorTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="form-factor" type="hidden" class="form-factor" value="0" />
       </div>

       <!--- Form factor filter end --->

       <!--- Memory type filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">მეხსიერების ტიპი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['memoryTypes'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="memory-type">
           <b>{{ $value -> typeTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="memory-type" type="hidden" class="memory-type" value="0" />
       </div>

       <!--- Memory type filter end --->

       <!--- Socket filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ბუდე</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['cpuSockets'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="cpu-socket">
           <b>{{ $value -> socketTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="cpu-socket" type="hidden" class="cpu-socket" value="0" />
       </div>

       <!--- Socket filter end --->

       <!--- Stock type filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">საწყობის ტიპი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['stockTypes'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="stock-type">
           <b class="font-6">{{ $value -> stockTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="stock-type" type="hidden" class="stock-type" value="0" />
       </div>

       <!--- Stock type filter end --->

       <!--- Condition filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">მდგომარეობა</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['conditions'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="condition">
           <b class="font-6">{{ $value -> conditionTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="condition" type="hidden" class="condition" value="0" />
       </div>

       <!--- Condition filter end --->
      </div>
     </form>
    </div>
   </div>
  </div>

  <!--- Motherboards list start --->

  <div class="product-list-column col-xs-12 col-sm-9">
   <div class="product-list-control-bar clearfix">
    <div class="product-list-control-bar-content">
     <div class="selects-wrapper">
      <div class="num-of-products-to-view-select cstm-select closed" tabindex="-2">
       <input type="hidden" name="numOfProductsToShow" class="cstm-parameter" value="9" />
       <div class="cstm-selected-option">
        <span> აჩვენეთ 9 </span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item cstm-active-option" data-option-value="9">
         <span> აჩვენეთ 9 </span>
        </div>
        @for($items = 12; $items <= 30; $items += 3)
        <div class="cstm-option-item" data-option-value="{{ $items }}">
         <span> აჩვენეთ {{ $items }} </span>
        </div>
        @endfor
       </div>
      </div>

      <div class="products-sort-select cstm-select closed" tabindex="-2">
       <input type="hidden" name="order" class="cstm-parameter" value="0" />
       <div class="cstm-selected-option">
        <span> დახარისხება ჩუმათობით</span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item cstm-active-option" data-option-value="0">
         <span> დახარისხება ჩუმათობით </span>
        </div>
        <div class="cstm-option-item" data-option-value="1">
         <span>ფასის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="2">
         <span>ფასის ზრდადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="3">
         <span>დამატების დროის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="4">
         <span>დამატების დროის ზრდადობით</span>
        </div>
       </div>
      </div>
     </div>

     <ul class="display-type-switch-block">
      <li id="grid">
       <a rel="nofollow" href="#" data-toggle="tooltip" data-placement="top">
        <i class="fas fa-th"></i>
       </a>
      </li>
      <li id="list">
       <a rel="nofollow" href="#" data-toggle="tooltip" data-placement="top">
        <i class="fas fa-list"></i>
       </a>
      </li>
      <input type="hidden" class="products-view-type" value="grid" />
     </ul>
    </div>
    <!-- Sort bar end -->
   </div>

   <div class="list-cart-notification-block">
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

   <!-- Products list -->
   <div id="result-content" data-products-category-id="{{ $contentData['productsCategoryId'] }}">
    <ul class="product-list grid row">
     @foreach($contentData['motherboards'] as $value)
     <li class="ajax-block-product col-md-4" data-product-id="{{ $value -> id }}" data-stock-type-id="{{ $value -> stockTypeId }}" data-product-title="{{ $value -> title }}" data-product-price="{{ $value -> newPrice }}">
      <div class="product-container">
       <div class="itw-display-product-info">
        <div class="left-block">
         <div class="product-image-container">
          <a target="_blank" href="/motherboards/{{ $value -> id }}" title="{{ $value -> title }}">
           <img class="replace-2x img-fluid" src="/images/motherboards/main/original/{{ $value -> mainImage }}" width="450" height="478" />
          </a>
          <div class="content-price">
           <span class="price product-price"> <b class="currency-gel">₾</b> {{ $value -> newPrice }} </span>
           @if($value -> newPrice != $value -> price)
           <span class="old-price product-price"> <b>₾</b> {{ $value -> price }} </span>
           @endif
          </div>
         </div>
         @if($value -> enableAddToCartButton)
         <div class="list-add-to-cart-container">
          <a class="add-to-cart-btn">
           <i class="fas fa-cart-plus"></i>
          </a>
         </div>
         @endif
        </div>

        <div class="right-block">
         <h5>
          <a class="product-name font-7" target="_blank" href="/motherboards/{{ $value -> id }}" title="{{ $value -> title }}">
           <span class="font-5">{{ $value -> title }} </span>
          </a>
         </h5>

         <div class="product-desc">
          <div>
           <b class="font-6">ბრენდი: </b>
           <span>{{ $value -> manufacturerTitle }}</span>
          </div>
          <div>
           <b class="font-6">ბუდე: </b>
           <span>{{ $value -> socketTitle }}</span>
          </div>
          <div>
           <b class="font-6">ფორმფაქტორი: </b>
           <span>{{ $value -> formFactorTitle }}</span>
          </div>
          <div>
           <b class="font-6">მეხსიერების ტიპი: </b>
           <span>{{ $value -> typeTitle }}</span>
          </div>
         </div>

         <div class="content-price">
          <span class="price product-price"> <b class="currency-gel">₾</b> {{ $value -> newPrice }} </span>

          @if($value -> newPrice != $value -> price)

          <span class="old-price product-price"> <b>₾</b> {{ $value -> price }} </span>
          @endif
         </div>
        </div>
       </div>
       <div class="product-more-options">
        <div class="read-more-container">
         <a class="button read-more-button btn btn-secondary" target="_blank" href="/motherboards/{{ $value -> id }}">
          <span class="font-5">დაწვრილებით</span>
         </a>
        </div>
        <div class="short-description">
         <div>
          <b class="font-6">ბრენდი: </b>
          <span>{{ $value -> manufacturerTitle }}</span>
         </div>
         <div>
          <b class="font-6">ბუდე: </b>
          <span>{{ $value -> socketTitle }}</span>
         </div>
         <div>
          <b class="font-6">ფორმფაქტორი: </b>
          <span>{{ $value -> formFactorTitle }}</span>
         </div>
         <div>
          <b class="font-6">მეხსიერების ტიპი: </b>
          <span>{{ $value -> typeTitle }}</span>
         </div>
        </div>
       </div>
      </div>
      <!-- .product-container> -->
     </li>
     @endforeach
    </ul>

    <div class="product-list-control-bar">
     <div class="bottom-pagination-content clearfix">
      <!-- Pagination -->
      <div id="bottom-pagination" class="clearfix">
       <ul class="pagination">

       @if($contentData['currentPage'] > 1)

        <li class="pagination-previous">
         <a href="/index/motherboards/{{ $contentData['currentPage'] - 1 }}" rel="prev">
          <i class="fas fa-chevron-left"></i>
         </a>
        </li>

        @endif

        @foreach($contentData['pages'] as $page)

        @if($page['isPad'])

        <li class="dots">
         <span>
          <span>{{ $page['value'] }}</span>
         </span>
        </li>

        @elseif($page['isActive'])

        <li class="active current">
         <span>
          <span>{{ $page['value'] }}</span>
         </span>
        </li>

        @else

        <li>
         <a href="/index/motherboards/{{ $page['value'] }}">
          <span>{{ $page['value'] }}</span>
         </a>
        </li>

        @endif

        @endforeach

        @if($contentData['currentPage'] < $contentData['maxPage'])

        <li class="pagination-next">
         <a href="/index/motherboards/{{ $contentData['currentPage'] + 1 }}" rel="next">
          <i class="fas fa-chevron-right"></i>
         </a>
        </li>

        @endif
       </ul>
      </div>
      <!-- /Pagination -->
     </div>
    </div>
   </div>
  </div>
  <!--- motherboards list end --->
 </div>
 <!-- .row -->
</div>

@include('parts.shop.list')

@else

<link rel="stylesheet" href="/css/no-products.css" type="text/css" />

<div class="container" id="no-products-container">
 <div class="columns-container">
  <div class="row">
   <div class="col-sm-12">
    <div class="warning-container">
     <h1 class="font-5">პროდუქცია ჯერ არ არის დამატებული!</h1>
    </div>
   </div>
  </div>
 </div>
</div>

@endif

@endsection
