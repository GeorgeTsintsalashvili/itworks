
@extends('layouts.shop')

@section('content')

<div id="cart-modal" style="width: 350px;border: 1px solid #3b3b3b;position: fixed;right: -350px;top: 50%;z-index: 2000;padding: 10px;background-color: #636363;">
 <div class="cart-modal-container">
  <span class="cart-modal-text" style="font-family: 'font-7';font-size: 14px; color: #fff;"> პროდუქტი კოდით d36fk-345 დამატებულია კალათაში </span>
 </div>
</div>

<script>

  let options = {
    "block": document.getElementById("cart-modal"),
    "time": 500,
    "gap": 10
  };

  function animate(options)
  {
    let width = parseInt(window.getComputedStyle(options["block"], null).getPropertyValue("width"));
    let timePercent = options["time"] / 100, widthPercent = width / 100;
    let timePercentElapsed = 0, widthPercentElapsed = 0;
    let offset = -width + options["gap"];

    let moveAnimationInterval = setInterval(() => {

      if (timePercentElapsed < options["time"])
      {
        timePercentElapsed += timePercent;
        widthPercentElapsed += widthPercent;

        options["block"].style.right = offset + widthPercentElapsed + "px";
      }

      else
      {
        clearInterval(moveAnimationInterval);

        setTimeout(() => options["block"].style.right = -width + "px", 5000);
      }

    }, timePercent);
  }

  animate(options);

</script>

<!--- Slider section start --->

@if($contentData['slidesExist'])
<section id="home-slider">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="splide">
     <div class="splide__track">
      <ul class="splide__list">
       @foreach($contentData['slides'] as $slide)
       <li class="splide__slide">
        <img data-u="image" src="/images/slides/original/{{ $slide -> image }}" />
       </li>
       @endforeach
      </ul>
     </div>
    </div>
    <div class="splide__progress">
     <div class="splide__progress__bar"></div>
    </div>
   </div>
  </div>
 </div>
</section>
@endif

<!--- Slider section end --->

<!--- Series section start --->

@if($contentData['cpuSeriesExist'] && $contentData['activeCpuSeriesId'])

<div class="blockPosition mt-5">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="dynamic-carousel clearfix show-hover2">
     <div class="itw-tab-content clearfix">
      <div class="itw-products-tab-wrapper">
       <div class="row-item-protab">
        <div class="protab-lists mb-4">
         <div class="pro-tab-head">
          <!--- tabproductcategory --->
          <ul role="tablist" class="nav nav-tabs carousel-tablist">
           @foreach($contentData['cpuSeries'] as $value)
           <li>
            <a class="{{ $contentData['activeCpuSeriesId'] == $value -> id ? 'active' : null }}" aria-expanded="false" href="#computer-systems" data-ajaxurl="{{ route('homePageComputers', ['id' => $value -> id]) }}" data-toggle="tab">
             <span class="font-5">{{ $value -> homePageTitle }}</span>
            </a>
           </li>
           @endforeach
          </ul>
         </div>
        </div>

        <div class="protab-contents">
         <div class="tab-content">
          <div id="computer-systems" class="tab-pane active in">
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
              @foreach($contentData['activeSystems'] as $value)

              <li class="ajax-block-product col-sm-3 position-relative splide__slide">
               <div class="product-container">
                <div class="itw-display-product-info">
                 <div class="left-block">
                  <div class="product-image-container">
                   <a href="/computers/{{ $value -> id }}" title="{{ $value -> title }}">
                    <img class="replace-2x img-fluid" src="/images/computers/main/original/{{ $value -> mainImage }}"/>
                   </a>
                  </div>
                 </div>

                 <div class="right-block">
                  <h5 itemprop="name">
                   <a class="product-name font-7" href="/computers/{{ $value -> id }}" title="{{ $value -> title }}">
                    <span class="font-5">{{ $value -> title }}</span>
                   </a>
                  </h5>

                  <div class="content-price">
                   @if($value -> discount != 0)

                   <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> newPrice }} </span>
                   <span class="old-price product-price">₾ {{ $value -> price }}</span>

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

@endif

<!--- Seris section end --->

<!--- Offer section start --->

@if($contentData['specialOffersExist'])
<div class="itw-products-section blockPosition itw-bg-gray">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="standard-products-carousel-block clearfix show-hover2">
     <div class="standard-products-carousel-data">
      <div class="fancy-heading text-left top-list-title">
       <h3>
        <span class="font-6"> ჩვენ გთავაზობთ</span>
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
             @foreach($contentData['specialOffers'] as $value)

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
@endif

<!--- Offer section end --->

<!--- Latest products section start ------------->

@if($contentData['latestProductsExist'])
<div class="itw-products-section blockPosition itw-bg-gray mt-2">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="standard-products-carousel-block clearfix show-hover2">
     <div class="standard-products-carousel-data">
      <div class="fancy-heading text-left top-list-title">
       <h3>
        <span class="font-6"> ბოლოს დამატებულები </span>
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
             @foreach($contentData['latestProducts'] as $value)

             <li class="ajax-block-product col-sm-3 position-relative splide__slide">
              <div class="product-container">
               <div class="itw-display-product-info">
                <div class="left-block">
                 <div class="product-image-container">
                  <a href="/{{ $value -> pathPart }}/{{ $value -> id }}">
                   <img class="replace-2x img-fluid" src="/images/{{ $value -> pathPart }}/main/original/{{ $value -> mainImage }}" />
                  </a>
                 </div>
                </div>

                <div class="right-block">
                 <h5>
                  <a class="product-name font-7" href="/{{ $value -> pathPart }}/{{ $value -> id }}">
                   {{ $value -> title }}
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
                 <a class="button read-more-button btn btn-secondary" href="/{{ $value -> pathPart }}/{{ $value -> id }}">
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
@endif

<!--- Latest products section end --->

<!--- Discounted products section start ------------->

@if($contentData['discountedProductsExist'])
<div class="itw-products-section blockPosition itw-bg-gray mt-5">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="standard-products-carousel-block clearfix show-hover2">
     <div class="standard-products-carousel-data">
      <div class="fancy-heading text-left top-list-title">
       <h3>
        <span class="font-6"> ფასდაკლებული პროდუქცია </span>
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
             @foreach($contentData['discountedProducts'] as $value)

             <li class="ajax-block-product col-sm-3 position-relative splide__slide">
              <div class="product-container">
               <div class="itw-display-product-info">
                <div class="left-block">
                 <div class="product-image-container">
                  <a href="/{{ $value -> pathPart }}/{{ $value -> id }}">
                   <img class="replace-2x img-fluid" src="/images/{{ $value -> pathPart }}/main/original/{{ $value -> mainImage }}" />
                  </a>
                 </div>
                </div>

                <div class="right-block">
                 <h5>
                  <a class="product-name font-7" href="/{{ $value -> pathPart }}/{{ $value -> id }}">
                   {{ $value -> title }}
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
                 <a class="button read-more-button btn btn-secondary" href="/{{ $value -> pathPart }}/{{ $value -> id }}">
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
@endif

<!--- Discounted products section end ----->

<!--- Accessories section start ------------->

@if($contentData['activeAccessoryCategoryId'] != 0)
<div class="itw-products-section blockPosition itw-bg-gray mt-5">
 <div class="container">
  <div class="row">
   <div class="col-lg-12 position-relative">
    <div class="dynamic-carousel clearfix show-hover2">
     <div class="itw-tab-content clearfix">
      <div class="itw-products-tab-wrapper">
       <div class="row-item-protab">
        <div class="protab-lists mb-4">
         <div class="pro-tab-head">
          <!--- tabproductcategory --->
          <ul role="tablist" class="nav nav-tabs carousel-tablist">
           @foreach($contentData['accessoriesTypes'] as $accessory)
           <li>
            <a class="{{ $accessory -> id == $contentData['activeAccessoryCategoryId'] ? 'active' : null }}" aria-expanded="false" href="#computer-accessories" data-ajaxurl="{{ route('homePageAccessories', ['id' => $accessory -> id]) }}" data-toggle="tab">
             <span class="font-5">{{ $accessory -> typeTitle }}</span>
            </a>
           </li>
           @endforeach
          </ul>
         </div>
        </div>

        <div class="protab-contents">
         <div class="tab-content">
          <div id="computer-accessories" class="tab-pane active in">
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
              @foreach($contentData['activeAccessories'] as $value)
              <li class="ajax-block-product col-sm-3 position-relative splide__slide">
               <div class="product-container">
                <div class="itw-display-product-info">
                 <div class="left-block">
                  <div class="product-image-container">
                   <a href="/accessories/{{ $value -> id }}" title="{{ $value -> title }}">
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

                   <span class="price product-price"><b class="currency-gel">₾</b> {{ $value -> newPrice }} </span>
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
@endif

<!--- Accessories section end --->

<script type="text/javascript">

(() => {

  document.addEventListener("DOMContentLoaded", () => {

    let options = {
      perPage: 1,
  	  type: "loop",
  	  autoplay: true,
  	  pauseOnHover: true,
      waitForTransition: 3000,
      arrows: ""
    }

    let splide = new Splide(".splide", options );

    splide.mount();

  });

})();

</script>

@endsection
