
<div class="part-select-head">
  <h1 class="font-6"> ყურსასმენები </h1>
   <a href="#" class="select-part-window-close-button">
    <i class="fa fa-times"></i>
   </a>
  </div>

  <div class="part-select-body">
   <div class="part-select-list container">

     @if($data['productsExist'])

     @foreach($data['products'] as $product)

     <div class="row parts-item">
      <div class="col-sm-2">
        <img src="/images/accessories/main/original/{{ $product -> mainImage }}" class="part-image">
      </div>
      <div class="col-sm-7">
       <div class="part-to-select-container">
         <h1 class="part-to-select-title font-4">{{ $product -> title }}</h1>
         <div class="part-to-select-description">

           <div class="part-specification">
             <b class="font-6">საწყობის ტიპი </b>
             <span class="font-5"> {{ $product -> stockTitle }}</span>
           </div>

           <div class="part-specification">
             <b class="font-6">მდგომარეობა </b>
             <span class="font-5"> {{ $product -> conditionTitle }}</span>
           </div>
        </div>
       </div>
      </div>

      <div class="col-sm-3">

        <div class="part-current-price-container">
         <span class="part-current-price">₾ {{ $product -> price - $product -> discount }} </span>
        </div>

        @if($product -> discount != 0)
        <div class="part-old-price-container">
          <span class="part-old-price">₾ {{ $product -> price }}</span>
        </div>
        @endif

        <div class="part-choose-button-container" data-part-id="{{ $product -> id }}" data-part-select-uri="/configurator/selectHeadphone">
          <button class="part-choose-button font-3"> დამატება </button>
        </div>

      </div>
     </div>

     @endforeach

   @else
    <h1 class="font-4 component-not-found-heading"> <i class="fa fa-exclamation-triangle"></i> ყურსასმენები ვერ მოიძებნა</h1>
   @endif

   </div>
  </div>
