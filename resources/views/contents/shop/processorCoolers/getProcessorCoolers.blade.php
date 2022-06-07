
@if($data['productsExist'])

<ul class="product-list grid row">
 @foreach($data['products'] as $value)
 <li class="ajax-block-product col-md-4" data-product-id="{{ $value -> id }}" data-stock-type-id="{{ $value -> stockTypeId }}" data-product-title="{{ $value -> title }}" data-product-price="{{ $value -> newPrice }}">
  <div class="product-container">
   <div class="itw-display-product-info">
    <div class="left-block">
     <div class="product-image-container">
      <a target="_blank" href="/processorCoolers/{{ $value -> id }}" title="{{ $value -> title }}">
       <img class="replace-2x img-fluid" src="/images/processorCoolers/main/original/{{ $value -> mainImage }}" width="450" height="478" />
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
      <a class="product-name font-7" target="_blank" href="/processorCoolers/{{ $value -> id }}" title="{{ $value -> title }}">
       <span class="font-5">{{ $value -> title }} </span>
      </a>
     </h5>

     <div class="product-desc">
      <div>
       <b class="font-6">სოკეტი: </b>
       <span class="font-5">{{ $value -> socketTitle }}</span>
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
     <a class="button read-more-button btn btn-secondary" target="_blank" href="/processorCoolers/{{ $value -> id }}">
      <span class="font-5">დაწვრილებით</span>
     </a>
    </div>
    <div class="short-description">
     <div>
      <b class="font-6">სოკეტი: </b>
      <span class="font-5">{{ $value -> socketTitle }}</span>
     </div>
    </div>
   </div>
  </div>
  <!-- .product-container> -->
 </li>
 @endforeach
</ul>

<!-- Pagination start-->

<div class="product-list-control-bar">
 <div class="bottom-pagination-content clearfix">
  <div id="bottom-pagination" class="clearfix">
   <ul class="pagination">
    @if($data['currentPage'] > 1)
    <li class="pagination-previous">
     <a href="{{ $data['currentPage'] - 1 }}" rel="prev">
      <i class="fas fa-chevron-left"></i>
     </a>
    </li>
    @endif

    @foreach($data['pages'] as $page)

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

    @if($data['currentPage'] < $data['maxPage'])

    <li class="pagination-next">
     <a href="{{ $data['currentPage'] + 1 }}" rel="next">
      <i class="fas fa-chevron-right"></i>
     </a>
    </li>

    @endif
   </ul>
  </div>
 </div>
</div>

<!-- Pagination end -->

@endif
