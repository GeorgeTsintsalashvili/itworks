
@if($computersExist)

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
    <!-- Products list -->
    <ul class="product-list splide__list">
     @foreach($computers as $value)
     <li class="ajax-block-product col-sm-3 position-relative splide__slide">
      <div class="product-container">
       <div class="itw-display-product-info">
        <div class="left-block">
         <div class="product-image-container">
          <a href="/computers/{{ $value -> id }}">
           <img class="replace-2x img-fluid" src="/images/computers/main/original/{{ $value -> mainImage }}" />
          </a>
         </div>
        </div>
        <div class="right-block">
         <h5>
          <a class="product-name font-7" href="/computers/{{ $value -> id }}">
           {{ $value -> title }}
          </a>
         </h5>
         <div class="content-price">
          @if($value -> discount != 0)

          <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> price - $value -> discount }} </span>
          <span class="old-price product-price">₾ {{ $value -> price }} </span>

          @else
          <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> price }}</span>
          @endif
         </div>
        </div>
       </div>

       <div class="product-more-options">
        <div class="read-more-container">
         <a class="button read-more-button btn btn-secondary" href="/computers/{{ $value -> id }}">
          <span class="font-5">დაწვრილებით</span>
         </a>
        </div>
        <div class="short-description">
         <div title="პროცესორი"><i class="fas fa-microchip"> </i> <span>{{ $value -> cpu }}</span></div>
         <div title="ოპერატიული"><i class="fas fa-memory"> </i> <span>{{ $value -> memory }} GB </span></div>
         <div title="გრაფიკა"><i class="flaticon-026-sound-card"> </i> <span>{{ $value -> gpuTitle }}</span></div>
         <div title="მეხსიერება"><i class="flaticon-005-hdd"> </i> <span>{{ $value -> storage }}</span></div>
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
