
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
    <span class="breadcrumb-part font-5">ძებნის შედეგები</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

<div class="container">
 <div class="row align-items-start">
  <div class="col-sm-3 filter">
   <div id="products-filter-block">
    <div class="block_content filter-item">
     <form action="{{ route('psrLoad') }}" class="filter-form" method="post" data-active-category-id="{{ $contentData['category-id'] }}">
      <div class="filter-form-container">
       <!--- active page parameter  --->

       <input name="active-page" type="hidden" class="active-page" value="1" />

       <!--- Price filter start --->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">ღირებულება ლარებში</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         <div class="input-range price-range" data-min-price="{{ $contentData['productMinPrice'] }}" data-max-price="{{ $contentData['productMaxPrice'] }}">
          <input name="price-from" type="text" class="price-from" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['productMinPrice'] }}" />
          <input name="price-to" type="text" class="price-to" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" value="{{ $contentData['productMaxPrice'] }}" />
         </div>
         <div class="range-slider-container"></div>
        </ul>
       </div>

       <!--- Price filter end ----->

       <!--- Categories filter start ---->

       <div class="filter-unit">
        @if($contentData['productsExist'])
        <h2 class="title-block">
         <span class="font-5">კატეგორიები</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['categories'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" checked class="checkbox type-of-checkbox" data-value="{{ $value['categoryId'] }}" data-active="1" data-hidden-input="category-id">
           <b class="font-6">{{ $value['categoryTitle'] }}</b>
           <span> ({{ $value['quantity'] }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        @endif
        @if($contentData['paramsAreValid'])
        <input name="category-id" type="hidden" class="category-id" value="{{ $contentData['categoryIdentifiers'] }}" />
        <input name="query" type="hidden" class="query" value="{{ $contentData['search-query'] }}" />
        @endif
       </div>

       <!--- Categories filter end ---->

       <!--- Stock type filter start ----->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">საწყობის ტიპი</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['stockTypes'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="stock-type">
           <b class="font-6">{{ $value -> stockTitle }}</b>
           <span> ({{ $value -> quantity }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="stock-type" type="hidden" class="stock-type" value="0" />
       </div>

       <!--- Stock type filter end ---->

       <!--- Condition filter start ----->

       <div class="filter-unit">
        <h2 class="title-block">
         <span class="font-5">მდგომარეობა</span>
        </h2>
        <ul class="col-lg-12 filter-block-item">
         @foreach($contentData['conditions'] as $key => $value)
         <li class="nomargin hiddable col-lg-12">
          <a href="#" class="checkbox" data-value="{{ $value -> id }}" data-active="0" data-hidden-input="condition">
           <b class="font-6">{{ $value -> conditionTitle }}</b>
           <span> ({{ $value -> quantity }})</span>
          </a>
         </li>
         @endforeach
        </ul>
        <input name="condition" type="hidden" class="condition" value="0" />
       </div>

       <!--- Condition filter end ------>
      </div>
     </form>
    </div>
   </div>
  </div>

  <!--- Products list start ----->

  @if($contentData['productsExist'])

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
        @for($items = 12; $items <= 30; $items+=3)
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
   </div>

   <!-- Products list -->

   <div id="result-content">
    <ul class="product-list grid row">
     @foreach($contentData['products'] as $value)
     <li class="ajax-block-product col-md-4">
      <div class="product-container">
       <div class="itw-display-product-info">
        <div class="left-block">
         <div class="product-image-container">
          <a target="_blank" href="/{{ $value -> pathPart }}/{{ $value -> id }}" title="{{ $value -> title }}">
           <img class="replace-2x img-fluid" src="/images/{{ $value -> pathPart }}/main/original/{{ $value -> mainImage }}" width="450" height="478" />
          </a>
          <div class="content-price">
           <span class="price product-price"> <b class="currency-gel">₾</b> {{ $value -> newPrice }} </span>
           @if($value -> newPrice != $value -> price)
           <span class="old-price product-price"> <b>₾</b> {{ $value -> price }} </span>
           @endif
          </div>
         </div>
        </div>

        <div class="right-block">
         <h5>
          <a class="product-name font-7" target="_blank" href="/{{ $value -> pathPart }}/{{ $value -> id }}" title="{{ $value -> title }}">
           <span class="font-5">{{ $value -> title }} </span>
          </a>
         </h5>

         <div class="product-desc"></div>

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
         <a class="button read-more-button btn btn-secondary" target="_blank" href="/{{ $value -> pathPart }}/{{ $value -> id }}">
          <span class="font-5">დაწვრილებით</span>
         </a>
        </div>
        <div class="short-description"></div>
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
         <a href="{{ $page['value'] }}">
          <span>{{ $page['value'] }}</span>
         </a>
        </li>

        @endif

				@endforeach

				@if($contentData['maxPage'] > 1)

        <li class="pagination-next">
         <a href="2" rel="next">
          <i class="fas fa-chevron-right"></i>
         </a>
        </li>

        @endif
       </ul>
      </div>
      <!-- /Pagination -->
     </div>
    </div>

    @endif
   </div>
  </div>
  <!--- List end ---->
 </div>
 <!-- .row -->
</div>

@include('parts.shop.list')

@endsection
