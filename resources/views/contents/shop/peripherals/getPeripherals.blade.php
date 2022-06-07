
@if($data['productsExist'])

<ul class="product-list grid row">
 @foreach($data['products'] as $value)
 <li class="ajax-block-product col-md-4" data-product-id="{{ $value -> id }}">
  <div class="product-container">
   <div class="itw-display-product-info">
    <div class="left-block">
     <div class="product-image-container">
      <a target="_blank" target="_blank" href="/peripherals/{{ $value -> id }}" title="{{ $value -> title }}">
       <img class="replace-2x img-fluid" src="/images/peripherals/main/original/{{ $value -> mainImage }}" width="450" height="478" />
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
      <a class="product-name font-7" target="_blank" href="/peripherals/{{ $value -> id }}" title="{{ $value -> title }}">
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
     <a class="button read-more-button btn btn-secondary" target="_blank" href="/peripherals/{{ $value -> id }}">
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
  <!-- Pagination start-->

  <div id="bottom-pagination" class="clearfix">
   <ul class="pagination">
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

    @if($data['maxPage'] > 1)

    <li class="pagination-next">
     <a href="2" rel="next">
      <i class="fas fa-chevron-right"></i>
     </a>
    </li>

    @endif
   </ul>
  </div>
  <!-- Pagination end -->
 </div>
</div>

@endif
