@if($data['productsExist'])

@foreach($data['products'] as $product)

<li>
 <a class="row" href="/{{ $product -> pathPart }}/{{ $product -> id }}" target="_blank">
  <div class="search-result-image-wrapper col-sm-2">
   <img src="/images/{{ $product -> pathPart }}/main/preview/{{ $product -> mainImage }}" />
  </div>
  <span class="col-sm-6 search-result-title font-4">{{ $product -> title }}</span>
  <div class="col-sm-4 search-result-price-block">
   <span class="search-result-price">₾ {{ $product -> price }} </span>
   @if($product -> discount != 0)
   <span class="search-result-discount">₾ {{ $product -> price + $product -> discount }}</span>
   @endif
  </div>
 </a>
</li>

@endforeach

@endif
