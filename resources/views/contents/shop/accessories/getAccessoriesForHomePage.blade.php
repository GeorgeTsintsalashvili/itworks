@if($accessoriesExist)

<!-- Products list -->

<div class="itw-content-items">
  <div class="splide__arrows">
    <button class="splide__arrow splide__arrow--prev">
     <i class="fas fa-angle-left"></i>
    </button>
    <button class="splide__arrow splide__arrow--next">
     <i class="fas fa-angle-right"></i>
    </button>
  </div>

  <div class="row-items splide__track">
    <ul class="product-list splide__list">
     @foreach($accessories as $value)
     <li class="ajax-block-product col-sm-3 position-relative splide__slide">
      <div class="product-container">
       <div class="itw-display-product-info">
        <div class="left-block">
         <div class="product-image-container">
          <a href="/accessories/{{ $value -> id }}" title="{{ $value -> mainImage }}">
           <img class="replace-2x img-fluid" src="/images/accessories/main/original/{{ $value -> mainImage }}"/>
          </a>
         </div>
        </div>

        <div class="right-block">
         <h5 itemprop="name">
          <a class="product-name font-7" href="/accessories/{{ $value -> id }}" title="{{ $value -> title }}">
           <span class="font-5">{{ $value -> title }}</span>
          </a>
         </h5>

         <div class="content-price">
          @if($value -> discount != 0)

          <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> price - $value -> discount }}</span>
          <span class="old-price product-price">₾ {{ $value -> price }}</span>

          @else
          <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> price }}</span>
          @endif
         </div>
        </div>
       </div>
       <div class="product-more-options">
        <div class="read-more-container">
         <a class="button read-more-button btn btn-secondary" href="/accessories/{{ $value -> id }}">
          <span class="font-5">დაწვრილებით</span>
         </a>
        </div>
       </div>
      </div>
      <!-- .product-container> -->
     </li>
     @endforeach
    </ul>
  </div>
</div>

@endif
