
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
    <span class="breadcrumb-part font-5">პროცესორები</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

@if($contentData['processorsExist'])
<div class="container">
 <div class="row align-items-start">
  <div class="col-sm-3 filter">
   <div id="products-filter-block">
    <div class="block_content filter-item">
     <form action="{{ route('cpuLoad') }}" class="filter-form" method="post">
      <div class="filter-form-container">
       <!--- active page parameter  --->
       <input name="active-page" type="hidden" class="active-page" value="{{ $contentData['currentPage'] }}" />

       <!--- Price filter start --->
       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ღირებულება ლარებში</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         <div class="input-range price-range" data-min-price="{{ $contentData['configuration']['productPriceRange'] -> processorMinPrice }}" data-max-price="{{ $contentData['configuration']['productPriceRange'] -> processorMaxPrice }}">
          <input name="price-from" type="text" class="price-from" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['productPriceRange'] -> processorMinPrice }}" />
          <input name="price-to" type="text" class="price-to" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['productPriceRange'] -> processorMaxPrice }}" />
         </div>
         <div class="range-slider-container"></div>
        </ul>
       </div>
       <!--- Price filter end ------>

       <!--- Clock speed filter start ---->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ტაქტური სიხშირე</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         <div class="input-range" data-speed-from="{{ $contentData['configuration']['minClockSpeed'] }}" data-speed-to="{{ $contentData['configuration']['maxClockSpeed'] }}">
          <input name="speed-from" type="text" class="speed-from" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['minClockSpeed'] }}" />
          <input name="speed-to" type="text" class="speed-to" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['configuration']['maxClockSpeed'] }}" />
         </div>
        </ul>
       </div>

       <!--- Clock speed filter end ------->

       <!--- Manufacturer filter start --------->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">მწარმოებელი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['cpuManufacturers'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="cpu-manufacturer">
           <b>{{ $value -> manufacturerTitle }}</b>
           <span> ({{ $value -> numOfProducts }}) </span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="cpu-manufacturer" type="hidden" class="cpu-manufacturer" value="0" />
       </div>

       <!--- Manufacturer filter end ----->

       <!--- CPU series filter start ------->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">პროცესორი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['cpuSeries'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="cpu-series">
           <b>{{ $value -> seriesTitle }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="cpu-series" type="hidden" class="cpu-series" value="0" />
       </div>

       <!--- CPU series filter end ----->

       <!--- Socket filter start ------->

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

       <!--- Socket filter end ----->

       <!--- Cores filter start ------>

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ბირთვები</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['cpuCores'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> cores }}" data-active="0" data-hidden-input="cores">
           <b>{{ $value -> cores }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="cores" type="hidden" class="cores" value="0" />
       </div>

       <!--- Cores filter end ---->

       <!--- Technology process filter start ------>

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ტექ. პროცესი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['configuration']['cpuTechnologyProcesses'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="technology-process">
           <b>{{ $value -> size }}</b>
           <span> ({{ $value -> numOfProducts }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="technology-process" type="hidden" class="technology-process" value="0" />
       </div>

       <!--- Stock filter end ------>

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

       <!--- Condition filter start ---->

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

       <!--- Condition filter end ---->
      </div>
     </form>
    </div>
   </div>
  </div>

  <!--- Processors list start ----->

  <div class="product-list-column col-xs-12 col-sm-9">
   <div class="product-list-control-bar clearfix">
    <div class="product-list-control-bar-content">
     <div class="selects-wrapper">
      <div class="num-of-products-to-view-select cstm-select closed" tabindex="-2">
       <input type="hidden" name="numOfProductsToShow" class="cstm-parameter" value="15" />
       <div class="cstm-selected-option">
        <span> აჩვენეთ 15 </span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item" data-option-value="9">
         <span> აჩვენეთ 9 </span>
        </div>
        <div class="cstm-option-item" data-option-value="12">
         <span> აჩვენეთ 12 </span>
        </div>
        <div class="cstm-option-item cstm-active-option" data-option-value="15">
         <span> აჩვენეთ 15 </span>
        </div>
        @for($items = 18; $items <= 30; $items += 3)
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
         <span>სიხშირის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="4">
         <span>სიხშირის ზრდადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="5">
         <span>ბირთვების კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="6">
         <span>ბირთვების ზრდადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="7">
         <span>დამატების დროის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="8">
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
     @foreach($contentData['processors'] as $value)
     <li class="ajax-block-product col-md-4" data-product-id="{{ $value -> id }}" data-stock-type-id="{{ $value -> stockTypeId }}" data-product-title="{{ $value -> title }}" data-product-price="{{ $value -> newPrice }}">
      <div class="product-container">

       <div class="itw-display-product-info">
        <div class="left-block">
         <div class="product-image-container">
          <a target="_blank" href="/processors/{{ $value -> id }}" title="{{ $value -> title }}">
           <img class="replace-2x img-fluid" src="/images/processors/main/original/{{ $value -> mainImage }}" width="450" height="478" />
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
          <a class="product-name font-7" target="_blank" href="/processors/{{ $value -> id }}" title="{{ $value -> title }}">
           <span class="font-5">{{ $value -> title }} </span>
          </a>
         </h5>

         <div class="product-desc">
          <div>
           <b class="font-6">სიხშირე: </b>
           <span>{{ $value -> clockSpeed }} Ghz</span>
          </div>
          <div>
           <b class="font-6">ტექნოლოგიური პროცესი: </b>
           <span>{{ $value -> size }}</span>
          </div>
          <div>
           <b class="font-6">ბირთვები: </b>
           <span>{{ $value -> cores }}</span>
          </div>
          <div>
           <b class="font-6">ბუდე: </b>
           <span>{{ $value -> socketTitle }}</span>
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
         <a class="button read-more-button btn btn-secondary" target="_blank" href="/processors/{{ $value -> id }}">
          <span class="font-5">დაწვრილებით</span>
         </a>
        </div>
        <div class="short-description">
         <div>
          <b class="font-6">სიხშირე: </b>
          <span>{{ $value -> clockSpeed }} Ghz</span>
         </div>
         <div>
          <b class="font-4" title="ტექნოლოგიური პროცესი">ტექ. პროცესი: </b>
          <span>{{ $value -> size }} </span>
         </div>
         <div>
          <b class="font-6">ბირთვები: </b>
          <span>{{ $value -> cores }}</span>
         </div>
         <div>
          <b class="font-6">ბუდე: </b>
          <span>{{ $value -> socketTitle }}</span>
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
         <a href="/index/processors/{{ $contentData['currentPage'] - 1 }}" rel="prev">
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
         <a href="/index/processors/{{ $page['value'] }}">
          <span>{{ $page['value'] }}</span>
         </a>
        </li>

        @endif

				@endforeach

				@if($contentData['currentPage'] < $contentData['maxPage'])

        <li class="pagination-next">
         <a href="/index/processors/{{ $contentData['currentPage'] + 1 }}" rel="next">
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
  <!--- Processors list end ------>
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
