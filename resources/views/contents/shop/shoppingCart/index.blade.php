@extends("layouts.shop")

@section("content")

<!-- breadcrumb start -->
<div class="breadcrumb">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<a class="breadcrumb-part breadcrumb-link" href="/">
					<i class="fa fa-home"></i>
				</a>
				<span class="breadcrumb-part font-5">კალათა</span>
			</div>
		</div>
	</div>
</div>
<!-- breadcrumb end -->

<!--- container --->
<div class="container mb-5">
 <div class="row">
  <div class="col-xs-12 col-sm-12">

   @if($contentData["shoppingCartIsNotEmpty"])

   <div class="shopping-cart-main-heading">
    <span class="shopping-cart-heading-text">თქვენ კალათაში არის
     <span id="summary-products-quantity"> {{ $contentData["numberOfProducts"] }} </span>
     <span> პროდუქტი </span>
		</span>
   </div>

   <!--- general notification block start --->

	 <div id="general-notification" class="shopping-cart-notification" style="display: none">
		<span class="font-7 notification-text">
		 <i class="fa fa-exclamation" aria-hidden="true"></i>
		 <a href="#general-notification" id="main-notification"></a>
		</span>
		<span id="general-notification-close">
		 <i class="fa fa-times"></i>
		</span>
	 </div>

	 <!--- shopping cart table block start --->
   <div class="table-block">
		<!--- shopping cart table start --->
    <div id="shopping-cart-table">

		 <!--- shopping cart head start --->
     <div class="cart-head">
      <div class="cart-row">
       <div class="cart-cell standard-cell">პროდუქცია</div>
       <div class="cart-cell standard-cell">კოდი</div>
       <div class="cart-cell standard-cell">რაოდენობა</div>
       <div class="cart-cell standard-cell">ფასი</div>
			 <div class="cart-cell standard-cell">წაშლა</div>
      </div>
		 </div>
		 <!--- shopping cart head end --->

     <!--- table body start --->
     <div class="cart-body">
			<!--- product item start --->
      @foreach($contentData["products"] as $value)
      <div class="cart-row" data-category-id="{{ $value -> product_category_id }}" data-product-id="{{ $value -> product_id }}" data-max-quantity="{{ $value -> quantity_limit }}">
       <!--- product description start --->
       <div class="cart-cell">
        <a target="_blank" href="{{ $value -> product_route }}">
         <img src="{{ $value -> product_image }}"/>
				 <h4 class="cart-product-title">
					<span>{{ $value -> product_title }}</span>
				 </h4>
        </a>
       </div>
			 <!--- product description end --->

       <!--- product code start --->
       <div class="cart-cell">
        <span class="product-code">{{ $value -> product_category_id }}-{{ $value -> product_id }}</span>
       </div>
			 <!--- product code end--->

       <!--- product quantity control start --->
       <div class="cart-cell">
        <div class="cart-quantity-button clearfix">
         <a class="cart-quantity-down btn btn-secondary button-minus" data-value="-1">
          <span>
           <i class="fas fa-minus"></i>
          </span>
         </a>
				 <input type="text" class="cart-quantity-input grey form-control" value="{{ $value -> cart_item_quantity }}" readonly/>
				 <a class="cart-quantity-up btn btn-secondary button-plus" data-value="1">
				  <span>
					 <i class="fas fa-plus"></i>
				  </span>
				 </a>
        </div>
       </div>
			 <!--- product quantity control end --->

       <!--- product price start --->
       <div class="cart-cell standard-cell">
        <span class="product-price"> ₾ {{ $value -> cart_item_price - $value -> cart_item_discount }} </span>
				@if($value -> cart_item_discount)
				<b class="product-old-price"> ₾ {{ $value -> cart_item_price }} </b>
				@endif
       </div>
			 <!--- product price end --->

			 <!--- product delete button start --->
       <div class="cart-cell">
        <a class="cart-product-delete-button">
				 <i class="fa fa-times"></i>
        </a>
       </div>
			 <!--- product delete button end --->
      </div>
			<!---  product item end --->
			@endforeach
     </div>
		 <!--- table body end --->

     <!--- table footer start --->
     <div class="cart-foot">
			<!--- total discount start --->
      <div class="cart-row">
       <div class="cart-cell">
        <span>საერთო ფასდაკლება</span>
       </div>
       <div class="cart-cell">
        <b>₾ </b>
        <span id="total-discount">{{ $contentData["totalDiscount"] }}</span>
       </div>
      </div>
			<!--- total discount end --->

      <!--- total price start --->
      <div class="cart-row">
       <div class="cart-cell">
        <span>საერთო ღირებულება</span>
       </div>
       <div class="cart-cell">
        <b>₾ </b>
        <span id="total-price">{{ $contentData["totalPrice"] }}</span>
       </div>
      </div>
			<!--- total price end --->
     </div>
		 <!--- table footer end --->
    </div>
		<!--- shopping cart table end --->
   </div>
	 <!--- shopping cart table block end --->

   <div id="shopping-cart-control-buttons">
		<button type="button" class="btn btn-secondary shadow-none" id="clear-button">კალათის გასუფთავება</button>
    <a href="{{ route('showOrderPrepare') }}" class="btn btn-primary shadow-none" id="order-button">შეკვეთის გაფორმება</a>
	 </div>

   @else
	 <div class="shopping-cart-main-heading cart-is-empty">
		<span class="shopping-cart-heading-text">ამჟამად თქვენი კალათა ცარიელია</span>
   </div>
   @endif

  </div>
  <!-- column -->
 </div>
 <!-- row -->
</div>
<!-- container -->

<link rel="stylesheet" href="/css/cart.css?v=32" type="text/css" />
<script type="text/javascript" src="/js/cart.js?v=32"></script>

@endsection
