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
    <span class="breadcrumb-part font-5">შეკვეთები</span>
   </div>
  </div>
 </div>
</div>
<!-- breadcrumb end -->

@if(!$contentData['numOfOrders'])
<div class="container">
 <div class="row align-items-start">
  <div class="col-sm-12">
    <div class="orders-main-heading orders-empty">
     <span class="orders-heading-text">თქვენი არ გაქვთ დამატებული შეკვეთები</span>
    </div>
  </div>
 </div>
</div>

@else

<div class="container mb-5">
 <div class="row">
  <div class="col-xs-12 col-sm-12">
   <div class="orders-main-heading">
    <span class="orders-heading-text">თქვენ გაქვთ განთავსებული
     <span id="orders-quantity"> {{ $contentData['numOfOrders'] }} </span>
     <span> შეკვეთა </span>
    </span>
   </div>

   <!--- general notification block start --->

	 <div id="general-notification" class="order-notification" style="display: none">
		<span class="font-7 notification-text">
		 <i class="fa fa-exclamation" aria-hidden="true"></i>
		 <a href="#general-notification" id="main-notification"></a>
		</span>
		<span id="general-notification-close">
		 <i class="fa fa-times"></i>
		</span>
	 </div>

   <div id="orders-filter-block">
    <div class="orders-filter-item">
      <div class="order-num-select cstm-select closed" tabindex="-2">
       <input type="hidden" value="1" class="cstm-parameter" id="order-type"/>
       <div class="cstm-selected-option">
        <span>დახარისხება დამატების დროის კლებადობით</span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item" data-option-value="1">
         <span>დახარისხება დამატების დროის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="2">
         <span>დახარისხება დამატების დროის ზრდადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="3">
         <span>დახარისხება ფასის კლებადობით</span>
        </div>
        <div class="cstm-option-item" data-option-value="4">
         <span>დახარისხება ფასის ზრდადობით</span>
        </div>
       </div>
      </div>
    </div>

    <div class="orders-filter-item">
      <div class="orders-type-select cstm-select closed" tabindex="-2">
       <input type="hidden" value="all" class="cstm-parameter" id="order-status"/>
       <div class="cstm-selected-option">
        <span>ყველა სტატუსის შეკვეთები</span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item" data-option-value="all">
         <span>ყველა სტატუსის შეკვეთები</span>
        </div>
        @foreach($contentData['orderStatuses'] as $orderStatus)
        <div class="cstm-option-item" data-option-value="{{ $orderStatus -> order_status_name }}">
         <span>{{ $orderStatus -> order_status_plural_title }}</span>
        </div>
        @endforeach
       </div>
      </div>
    </div>

    <div class="orders-filter-item">
      <div class="num-of-orders-to-view-select cstm-select closed" tabindex="-2">
       <input type="hidden" value="5" class="cstm-parameter" id="items-to-view"/>
       <div class="cstm-selected-option">
        <span>5 შეკვეთის ჩვენება</span>
       </div>
       <div class="cstm-options-container">
        <div class="cstm-option-item" data-option-value="5">
         <span>5 შეკვეთის ჩვენება</span>
        </div>
        <div class="cstm-option-item" data-option-value="10">
         <span>10 შეკვეთის ჩვენება</span>
        </div>
        <div class="cstm-option-item" data-option-value="15">
         <span>15 შეკვეთის ჩვენება</span>
        </div>
        <div class="cstm-option-item" data-option-value="20">
         <span>20 შეკვეთის ჩვენება</span>
        </div>
       </div>
      </div>
    </div>
   </div>

   <div id="orders-block">
    @foreach($contentData['orders'] as $order)
    <div class="order-item" data-order-id="{{ $order -> id }}">
     <div class="order-row">
      <div class="order-info-key">
       <b>შეკვეთის აიდი</b>
      </div>
      <div class="order-info-value">
       <span class="order-id-info">{{ $order -> id }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>შემკვეთის სახელი და გვარი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> customer_name }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>შემკვეთის ტელეფონი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> customer_phone }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>განთავსების თარიღი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> order_placement_date }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>შეკვეთის ღირებულება</b>
      </div>
      <div class="order-info-value">
       <span>₾ {{ $order -> order_price }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>მიწოდების მეთოდი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> deliver ? "ადგილზე მიწოდება" : "მაღაზიიდან გატანა" }}</span>
      </div>
     </div>
     @if($order -> delivery_address)
     <div class="order-row">
      <div class="order-info-key">
       <b>მიწოდების მისამართი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> delivery_address }}</span>
      </div>
     </div>
     @endif
     <div class="order-row">
      <div class="order-info-key">
       <b>გადახდის მეთოდი</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> payment_method_title }}</span>
      </div>
     </div>
     <div class="order-row">
      <div class="order-info-key">
       <b>შეკვეთის სტატუსი</b>
      </div>
      <div class="order-info-value">
       <span style="color: {{ $order -> order_status_color }}">{{ $order -> order_status_title }}</span>
      </div>
     </div>
     @if($order -> paid_at)
     <div class="order-row">
      <div class="order-info-key">
       <b>გადახდის თარიღი და დრო</b>
      </div>
      <div class="order-info-value">
       <span>{{ $order -> paid_at }}</span>
      </div>
     </div>
     @endif
     @if($order -> order_status_name == 'confirmed')
     <div class="order-row payment-deadline-row" data-payment-deadline="{{ $order -> payment_deadline }}" style="display: none">
      <div class="order-info-key">
       <b>გადახდამდე დარჩენილი დრო</b>
      </div>
      <div class="order-info-value">
       <span></span>
      </div>
     </div>
     @endif
     <div class="order-item-buttons-row">
      <button class="btn btn-orange shadow-none order-print-button">
        <i class="fas fa-print"></i>
        <span>შეკვეთის ამობეჭდვა</span>
      </button>
      <button class="btn btn-standard shadow-none products-visibility-control-button">
        <i class="far fa-eye"></i>
        <span>პროდუქციის ნახვა</span>
      </button>
      <button class="btn btn-standard shadow-none order-id-copy-button">
        <i class="far fa-copy"></i>
        <span>აიდის დაკოპირება</span>
      </button>
      @if($order -> order_status_name == 'confirmed' && $order -> payment_method_class == 'card')
      <a class="btn btn-green shadow-none order-pay-button" href="/shop/purchaseOrder/{{ $order -> id }}">
       <span>ბარათით გადახდა</span>
      </a>
      @elseif($order -> order_status_name == 'confirmed' && $order -> payment_method_class == 'installment' && $order -> payment_method_provider == 'bog')
      <div class="btn bog-smart-button" data-order-price="{{ $order -> order_price }}">განვადების მოთხოვნა</div>
      @elseif($order -> order_status_name == 'pending')
      <button class="order-cancel-button btn btn-standard shadow-none">
       <i class="fas fa-ban"></i>
       <span> შეკვეთის გაუქმება </span>
      </button>
      @endif
      @if($order -> allow_delete)
      <button class="btn btn-red shadow-none order-delete-button">
        <i class="far fa-trash"></i>
        <span>შეკვეთის წაშლა</span>
      </button>
      @endif
     </div>
     <div class="order-products-list" data-product-list-visibility="0" style="display: none">
       @foreach($order -> order_items as $orderItem)
       <div class="order-item-row">
         <div class="order-item-col">
          <a href="{{ $orderItem -> product_route }}" target="_blank">
           <img src="{{ $orderItem -> product_image }}">
          </a>
         </div>
         <div class="order-item-col">
          <div class="order-item-col-title">დასახელება</div>
          <div class="order-item-col-info">
           <a href="{{ $orderItem -> product_route }}" target="_blank">{{ $orderItem -> product_title }}</a>
          </div>
         </div>
         <div class="order-item-col">
          <div class="order-item-col-title">კოდი</div>
          <div class="order-item-col-info">
           <span>{{ $orderItem -> product_category_id }}-{{ $orderItem -> product_id }}</span>
          </div>
         </div>
         <div class="order-item-col">
          <div class="order-item-col-title">რაოდენობა</div>
          <div class="order-item-col-info">
           <span>{{ $orderItem -> order_item_quantity }} ერთეული</span>
          </div>
         </div>
         <div class="order-item-col">
          <div class="order-item-col-title">ფასი</div>
          <div class="order-item-col-info">
           <span>₾ {{ $orderItem -> order_item_price }}</span>
          </div>
         </div>
       </div>
       @endforeach
     </div>
    </div>
    @endforeach
   </div>
   <div id="orders-pagination-block" data-orders-num="{{ $contentData['numOfOrders'] }}" data-items-to-view="{{ $contentData['itemsToView'] }}" data-active-page="1" data-orders-list-address="{{ route('shopUserOrdersList') }}">
    <ul class="pagination">
    </ul>
   </div>
  </div>
 </div>
</div>

@endif

<style type="text/css">

.orders-main-heading.orders-empty {
  margin: 150px 0 150px 0;
}

.orders-heading-text {
  font-size: 25px;
  font-weight: 400;
  font-family: font-8;
}

.orders-main-heading {
  background: #f8f7f7 none repeat scroll 0 0;
  color: #333;
  font-size: 35px;
  font-weight: 300;
  padding: 40px 0;
  text-align: center;
  margin-bottom: 60px;
  border: 1px solid #e5e5e5;
}

#general-notification.order-notification {
  padding: 20px;
}

#general-notification.order-notification #main-notification {
  font-size: 16px;
  color: #575757;
}

#general-notification.order-notification .fa-exclamation {
  font-size: 20px;
  color: #f2aa02;
}

#general-notification.order-notification #general-notification-close i {
  right: 20px;
  font-size: 20px;
}

#orders-quantity {
  color: #0a756a;
}

/* orders style */

#orders-block .order-item {
  border: 1px solid #a8a8a8;
  margin-bottom: 50px;
  background-color: #fafafa;
}

#orders-block .order-row {
  display: flex;
  justify-content: space-between;
  padding: 10px;
}

#orders-block .order-row:not(:last-child) {
  border-bottom: 1px solid #d4d4d4;
}

#orders-block .order-item {
  position: relative;
}

#orders-block .order-item:not(:last-child)::after {
  content: "";
  border-top: 3px solid #0a756a;
  width: 100px;
  position: absolute;
  bottom: -26px;
  left: calc(50% - 50px);
}

#orders-block .order-item:not(:last-child)::before {
  content: "";
  border-top: 1px solid #0a756a;
  width: 300px;
  position: absolute;
  bottom: -25px;
  left: calc(50% - 150px);
}

#orders-block .order-info-key {
  margin-right: 10px;
  font-size: 14px;
  font-family: font-8;
  width: 40%;
}

#orders-block .order-info-value {
  font-size: 14px;
  font-family: font-7;
  width: 60%;
  text-align: right;
}

/* buttons style */

#orders-block .order-item-buttons-row .btn {
  margin-top: 6px;
}

#orders-block .order-item-buttons-row .btn:not(:last-child) {
  margin-right: 10px;
}

#orders-block .btn-orange,
#orders-block .btn-green,
#orders-block .btn-standard,
#orders-block .btn-red {
  color: #fff;
  border-radius: 0;
  font-family: font-6;
  font-size: 14px;
}

#orders-block .btn-orange {
  background-color: #ffb200;
  border: 1px solid #ffb200;
}

#orders-block .btn-red {
  border: 1px solid #ea3030;
  background-color: #ea3030;
}

#orders-block .btn-green {
  background-color: #0a756a;
  border: 1px solid #0a756a;
}

#orders-block .btn-standard {
  background-color: #595959;
  border: 1px solid #595959;
}

#orders-block .btn-red:hover {
  color: #ea3030;
  background-color: #fff;
}

#orders-block .btn-standard:hover {
  background-color: #fff;
  color: #595959;
}

#orders-block .btn-green:hover {
  background-color: #fff;
  color: #0a756a;
}

#orders-block .btn-orange:hover {
  color: #ffb200;
  background-color: #fff;
}

#orders-block .order-item-buttons-row {
  padding: 16px 10px 20px 10px;
}

#orders-block .payment-deadline-row .order-info-value {
  color: #ea3030;
  font-weight: bold;
}

/* order item style */

.order-item-row:first-child {
  margin-top: 20px;
}

.order-products-list {
  margin-top: 10px;
  border-top: 1px solid #d4d4d4;
}

.order-item-row {
  padding: 12px;
  display: flex;
  justify-content: space-between;
}

.order-item-col {
  width: 20%;
}

.order-item-col:not(:first-child) {
  text-align: center;
}

.order-item-col:not(:last-child) {
  margin-right: 30px;
}

.order-item-row img {
  background-color: #fff;
  border: 1px solid #d4d4d4;
  padding: 10px;
  width: 150px;
}

.order-item-row img:hover {
  border-color: #a8a8a8;
}

.order-item-col-title {
  font-size: 16px;
  font-family: font-8;
  font-weight: bold;
  color: #595959;
  margin-bottom: 20px;
}

.order-item-col-info {
  font-family: font-7;
  font-size: 16px;
}

.order-item-col-info a {
  text-decoration: none;
  color: #4f4f4f;
}

.order-item-col-info a:hover {
  text-decoration: underline;
  color: #0a756a;
}

/* customize bog style */

.bog-smart-modal .bog-smart-modal-wrapper,
.bog-smart-button {
  border-radius: 0;
}

.order-item .bog-smart-button {
  border: 1px solid #ff600a;
  font-family: font-6;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.5 !important;
  max-height: none;
  height: 100%;
  display: inline-block;
  text-align: center;
  text-decoration: none;
  vertical-align: middle;
  padding: 0.375rem 0.75rem;
  margin-top: 6px;
  box-shadow: none;
}

.order-item .bog-smart-button:hover {
  background-color: #fff;
  color: #ff600a;
}

/* pagination style */

#orders-pagination-block .pagination {
  display: flex;
  padding-left: 0;
  list-style: none;
  flex-wrap: wrap;
}

#orders-pagination-block .pagination li.pagination-previous {
  margin-right: 0px;
}

#orders-pagination-block .pagination li {
  display: inline-block;
  margin-bottom: 10px;
  float: left;
}

#orders-pagination-block .pagination li + li {
  padding-left: 10px;
}

#orders-pagination-block .pagination li.active.current > span,
#orders-pagination-block .pagination li.disabled.pagination-previous > span,
#orders-pagination-block .pagination li.disabled.pagination-next > span,
#orders-pagination-block .pagination li a {
  background: transparent none repeat scroll 0 0;
  border: 1px solid #bbbbbb;
  color: #5b5b5b;
  display: block;
  font-size: 14px;
  height: 40px;
  line-height: 38px;
  padding: 0;
  text-align: center;
  width: 40px;
  font-weight: 400;
}

#orders-pagination-block .pagination li a span,
#orders-pagination-block .pagination li.active.current > span span {
  background: none !important;
  border: none;
  padding: 0px;
}

#orders-pagination-block .pagination li > a span,
#orders-pagination-block .pagination li > span span {
  display: block;
}

#orders-pagination-block .pagination li.active.current > span,
#orders-pagination-block .pagination li > a:hover {
  background-color: #4b4b4b;
  border-color: #4b4b4b;
  color: #fff;
}

#orders-pagination-block .pagination .dots {
  display: block;
  padding: 6px 10px;
  border: 1px solid #bbbbbb;
  margin-left: 10px;
}

/* orders filter style */

#orders-filter-block {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  margin-bottom: 40px;
  padding-bottom: 30px;
  border-bottom: 1px solid #d4d4d4;
}

#orders-filter-block .cstm-selected-option {
  font-size: 14px;
  font-family: 'font-8';
  color: #474747;
  padding: 14px;
}

#orders-filter-block .cstm-option-item:hover,
#orders-filter-block .cstm-option-item.cstm-active-option {
  background-color: #595959;
  color: #ffffff;
}

#orders-filter-block .cstm-option-item {
  color: #272727
}

#orders-filter-block .cstm-options-container {
  margin-top: -1px;
  border-top: none;
}

#orders-filter-block .orders-filter-item {
  margin-bottom: 10px;
}

#orders-filter-block .orders-filter-item .cstm-option-item {
  word-break: keep-all;
}

@media (max-width: 768px) {

  .order-item-row img {
    width: 100%;
  }

  .order-item-col:not(:last-child),
  .order-item-col:last-child {
    margin: 20px auto;
  }

  .order-item-row:not(:last-child) {
    border-bottom: 1px solid #d4d4d4;
  }

  .order-item-row {
    flex-direction: column;
  }

  .order-item-col {
    width: 60%;
    text-align: center;
  }
}

/* no orders style */

.no-orders-text-container {
  background-color: #f8f7f7;
  border: 1px solid #aeaeae;
}

.no-orders-text-container h5 {
  text-align: center;
  padding: 40px 0;
  font-size: 32px;
  font-family: font-6;
}

</style>

<link rel="stylesheet" href="/css/select.css?v=35" type="text/css" media="all" />
<script type="text/javascript" src="/js/select.js?v=35"></script>
<script src="https://webstatic.bog.ge/bog-sdk/bog-sdk.js?client_id={{ $contentData['bogClientId'] }}"></script>

<script type="text/javascript">

let filterOptions = {
  "orderNum": 1,
  "ordersType": "all"
};

function updateTime(targetRow, paymentDate)
{
  let clientDate = new Date();
  let timeDifference = paymentDate - clientDate;
  let timeTextElement = targetRow.querySelector(".order-info-value span");

  if (timeDifference > 0)
  {
    let hoursLeft = Math.floor(timeDifference / (3600 * 1000));
    let minutesLeft = Math.floor(timeDifference / (60 * 1000)) % 60;
    let secondsLeft = Math.floor(timeDifference / 1000) % 60;
    let timeStr = `${hoursLeft < 10 ? "0" + hoursLeft : hoursLeft} საათი ${minutesLeft < 10 ? "0" + minutesLeft : minutesLeft} წუთი ${secondsLeft < 10 ? "0" + secondsLeft : secondsLeft} წამი`;

    timeTextElement.innerText = timeStr;
  }
}

function controlProductsListButtons()
{
  let orders = document.querySelectorAll(".order-item");

  orders.forEach((order, index) => {

    let visibilityControlButton = order.querySelector(".products-visibility-control-button");
    let orderPrintButton = order.querySelector(".order-print-button");
    let orderIdCopyButton = order.querySelector(".order-id-copy-button");
    let productsList = order.querySelector(".order-products-list");
    let productIdElement = order.querySelector(".order-id-info");
    let orderDeleteButton = order.querySelector(".order-delete-button");
    let orderId = order.getAttribute("data-order-id");
    let bogInstallmentButton = order.querySelector(".bog-smart-button");
    let orderCancelButton = order.querySelector(".order-cancel-button");
    let paymentDeadlineRow = order.querySelector(".payment-deadline-row");

    if (paymentDeadlineRow)
    {
      let unixSeconds = Number(paymentDeadlineRow.getAttribute("data-payment-deadline"));
      let unixMilliseconds = unixSeconds * 1000;
      let paymentDate = new Date(unixMilliseconds);

      paymentDeadlineRow.style.display = "flex";

      window.setInterval(() => updateTime(paymentDeadlineRow, paymentDate), 1000);
    }

    if (orderCancelButton)
    {
      orderCancelButton.addEventListener("click", () => {

        let orderCancelAddress = `/shop/order/cancel/${orderId}`;

        cancelOrder(orderCancelAddress);

      });
    }

    if (orderDeleteButton)
    {
      orderDeleteButton.addEventListener("click", () => {

        let orderDeleteAddress = `/shop/order/delete/${orderId}`;

        deleteOrder(orderDeleteAddress);

      });
    }

    if (bogInstallmentButton)
    {
      bogInstallmentButton.addEventListener("click", () => {
        BOG.Calculator.open({
          amount: bogInstallmentButton.getAttribute("data-order-price"),
          onClose: () => {
             // Modal close callback
          },
          onRequest: (selected, successCb, closeCb) => {

            const { amount, month, discount_code } = selected;

            selected["orderId"] = orderId;
            selected["_token"] = document.querySelector("meta[name=scrt]").getAttribute("content");

            let formData = new FormData();

            for (let key in selected)
            {
              formData.append(key, selected[key]);
            }

            console.log(selected);

            fetch("/shop/requireBogInstallment", {
              method: "POST",
              body: formData
            })
            .then(response => response.json())
            .then(data => {

              if (data["redirect"])
              {
                window.location.href = data["redirect"];
              }

              else
              {
                console.log(data["error"]);

                err => closeCb();
              }
            })
            .catch(err => closeCb());
          },
          onComplete: (redirectUrl) => {
            return false;
          }
        });
      });
    }

    orderIdCopyButton.addEventListener("click", () => {

      let range = document.createRange("input");

      range.selectNode(productIdElement);

      window.getSelection().removeAllRanges();
      window.getSelection().addRange(range);

      document.execCommand("copy");
      window.getSelection().removeAllRanges();

    });

    orderPrintButton.addEventListener("click", () => {

      let orderWindow = window.open(`/shop/order/${orderId}`);

      orderWindow.focus();
      orderWindow.print();

    });

    visibilityControlButton.addEventListener("click", () => {

      let productsListVisibility = Number(productsList.getAttribute("data-product-list-visibility"));
      let visibilityControlButtonTitle = visibilityControlButton.querySelector("span");

      if (productsListVisibility)
      {
        productsList.style.display = "none";
        visibilityControlButtonTitle.innerText = "პროდუქციის ნახვა";

        productsList.setAttribute("data-product-list-visibility", 0);
      }

      else
      {
        productsList.style.display = "block";
        visibilityControlButtonTitle.innerText = "პროდუქციის დამალვა";

        productsList.setAttribute("data-product-list-visibility", 1);
      }

    });

  });
}

controlProductsListButtons();

// control pagination

let paginationBlock = document.getElementById("orders-pagination-block");
let ordersBlock = document.getElementById("orders-block");
let orderListAddress = paginationBlock.getAttribute("data-orders-list-address");
let requestIsBeingProcessed = false;

let ordersPagination = document.querySelector(".pagination");

let paginationOptions = {
  "itemsToView": Number(paginationBlock.getAttribute("data-items-to-view")),
  "numOfItems": Number(paginationBlock.getAttribute("data-orders-num")),
  "curPage": Number(paginationBlock.getAttribute("data-active-page")),
  "adjacent": 3,
  "leftBoundary": 2,
  "rightBoundary": 2
};

function renderPagination(paginationOptions, pagesContainer)
{
  let paginator = new Paginator();

  paginator.build(paginationOptions);

  let pages = paginator.getPages();
  let maxPage = paginator.getMaxPage();

  if (paginationOptions["curPage"] > maxPage)
  {
    paginationOptions["curPage"] = maxPage;
  }

  if (paginationOptions["curPage"] > 1)
  {
    let previousButton = document.createElement("li");
    let link = document.createElement("a");
    let icon = document.createElement("i");
    let previousPage = paginationOptions["curPage"] - 1;

    link.setAttribute("href", previousPage);

    previousButton.setAttribute("class", "pagination-previous");
    icon.setAttribute("class", "fas fa-chevron-left");

    link.appendChild(icon);
    previousButton.appendChild(link);
    pagesContainer.appendChild(previousButton);

    previousButton.addEventListener("click", (event) => {

      event.preventDefault();

      if (!requestIsBeingProcessed)
      {
        let formData = {
          "curPage": String(previousPage),
          "itemsToView": String(paginationOptions["itemsToView"]),
          "orderNum": String(filterOptions["orderNum"]),
          "ordersType": filterOptions["ordersType"]
        };

        requestOrders("POST", orderListAddress, formData)

        paginationOptions["curPage"] = previousPage;

        pagesContainer.innerHTML = "";

        paginationOptions["curPage"] = previousPage;

        renderPagination(paginationOptions, pagesContainer);
      }

    });
  }

  for (let index in pages)
  {
    let paginationItem = document.createElement("li")

    if (pages[index]["isPad"])
    {
      paginationItem.setAttribute("class", "dots");

      let outerSpan = document.createElement("span");
      let innerSpan = document.createElement("span");

      innerSpan.innerText = pages[index]["value"];

      outerSpan.appendChild(innerSpan);
      paginationItem.appendChild(outerSpan);
    }

    else if(pages[index]["isActive"])
    {
      paginationItem.setAttribute("class", "active current");

      let outerSpan = document.createElement("span");
      let innerSpan = document.createElement("span");

      innerSpan.innerText = pages[index]["value"];

      outerSpan.appendChild(innerSpan);
      paginationItem.appendChild(outerSpan);
    }

    else
    {
      let link = document.createElement("a");
      let span = document.createElement("span");
      let page = pages[index]["value"];

      link.setAttribute("href", page);
      span.innerText = page;
      link.appendChild(span);
      paginationItem.appendChild(link);

      link.addEventListener("click", (event) => {

        event.preventDefault();

        if (!requestIsBeingProcessed)
        {
          let formData = {
            "curPage": String(page),
            "itemsToView": String(paginationOptions["itemsToView"]),
            "orderNum": String(filterOptions["orderNum"]),
            "ordersType": filterOptions["ordersType"]
          };

          requestOrders("POST", orderListAddress, formData)

          paginationOptions["curPage"] = page;

          pagesContainer.innerHTML = "";

          paginationOptions["curPage"] = page;

          renderPagination(paginationOptions, pagesContainer);
        }

      })
    }

    pagesContainer.appendChild(paginationItem);
  }

  if (paginationOptions["curPage"] < maxPage)
  {
    let nextButton = document.createElement("li");
    let link = document.createElement("a");
    let icon = document.createElement("i");
    let nextPage = paginationOptions["curPage"] + 1;

    link.setAttribute("href", nextPage);

    nextButton.setAttribute("class", "pagination-next");
    icon.setAttribute("class", "fas fa-chevron-right");

    link.appendChild(icon);
    nextButton.appendChild(link);
    pagesContainer.appendChild(nextButton);

    nextButton.addEventListener("click", (event) => {

      event.preventDefault();

      if (!requestIsBeingProcessed)
      {
        let formData = {
          "curPage": String(nextPage),
          "itemsToView": String(paginationOptions["itemsToView"]),
          "orderNum": String(filterOptions["orderNum"]),
          "ordersType": filterOptions["ordersType"]
        };

        requestOrders("POST", orderListAddress, formData)

        paginationOptions["curPage"] = nextPage;

        pagesContainer.innerHTML = "";

        paginationOptions["curPage"] = nextPage;

        renderPagination(paginationOptions, pagesContainer);
      }

    });
  }
}

renderPagination(paginationOptions, ordersPagination);

// control filter

let itemsToViewSelect = new CustomSingleSelect(".num-of-orders-to-view-select", function(){

    let selectedValue = this.querySelector("input").value;

    paginationOptions["itemsToView"] = selectedValue;

    if (!requestIsBeingProcessed)
    {
      let formData = {
        "curPage": String(paginationOptions["curPage"]),
        "itemsToView": String(paginationOptions["itemsToView"]),
        "orderNum": String(filterOptions["orderNum"]),
        "ordersType": filterOptions["ordersType"]
      };

      requestOrders("POST", orderListAddress, formData);

      ordersPagination.innerHTML = "";

      renderPagination(paginationOptions, ordersPagination);
    }
});

let ordersTypeSelect = new CustomSingleSelect(".order-num-select", function(){

    let selectedValue = this.querySelector("input").value;

    filterOptions["orderNum"] = selectedValue;

    if (!requestIsBeingProcessed)
    {
      let formData = {
        "curPage": String(paginationOptions["curPage"]),
        "itemsToView": String(paginationOptions["itemsToView"]),
        "orderNum": String(filterOptions["orderNum"]),
        "ordersType": filterOptions["ordersType"]
      };

      requestOrders("POST", orderListAddress, formData);
    }
});

let orderTypeSelect = new CustomSingleSelect(".orders-type-select", function(){

    let selectedValue = this.querySelector("input").value;

    filterOptions["ordersType"] = selectedValue;

    if (!requestIsBeingProcessed)
    {
      let formData = {
        "curPage": String(paginationOptions["curPage"]),
        "itemsToView": String(paginationOptions["itemsToView"]),
        "orderNum": String(filterOptions["orderNum"]),
        "ordersType": filterOptions["ordersType"]
      };

      requestOrders("POST", orderListAddress, formData);
    }
});

// request orders list

function requestOrders(method, address, data)
{
  requestIsBeingProcessed = true;

  axios({
    method: method,
    url: address,
    data: data
  })
  .then((response) => {

    let data = response["data"];

    if (data["numOfOrders"])
    {
      ordersBlock.innerHTML = data["payload"];

      controlProductsListButtons();

      let numOfPaginationItems = ordersPagination.querySelectorAll("li").length;

      if (data["numOfOrders"] != paginationOptions["numOfItems"] || numOfPaginationItems == 0)
      {
        ordersPagination.innerHTML = "";

        paginationOptions["numOfItems"] = data["numOfOrders"];

        renderPagination(paginationOptions, ordersPagination);
      }
    }

    else if (data["noOrdersExist"])
    {
      paginationOptions["numOfItems"] = 0;

      ordersPagination.innerHTML = "";

      ordersBlock.innerHTML = data["payload"];
    }

    else console.log(data);

    requestIsBeingProcessed = false;

  })
  .catch((error) => {

    let data = error.response["data"];

    if (data["tokenMismatch"])
    {
      console.log("Invalid token");
    }

    console.log(data);

    requestIsBeingProcessed = false;

  });
}

// order delete logic

function deleteOrder(deleteAddress)
{
  if (!requestIsBeingProcessed)
  {
    requestIsBeingProcessed = true;

    axios({
      method: "get",
      url: deleteAddress
    })
    .then((response) => {

      let data = response["data"];

      if (data["deleted"])
      {
        console.log("Order has been deleted");

        reloadOrders(paginationOptions);
      }

      requestIsBeingProcessed = false;

    })
    .catch((error) => {

      let data = error.response["data"];

      if (data["tokenMismatch"])
      {
        console.log("Invalid token");
      }

      console.log(data);

      requestIsBeingProcessed = false;

    });
  }

  else
  {
    console.log("Wait until the current request is processed");
  }
}

// cancel order logic

function cancelOrder(cancelAddress)
{
  if (!requestIsBeingProcessed)
  {
    requestIsBeingProcessed = true;

    axios({
      method: "get",
      url: cancelAddress
    })
    .then((response) => {

      let data = response["data"];

      if (data["canceled"])
      {
        console.log("Order has been canceled");

        reloadOrders(paginationOptions);
      }

      requestIsBeingProcessed = false;

    })
    .catch((error) => {

      let data = error.response["data"];

      if (data["tokenMismatch"])
      {
        console.log("Invalid token");
      }

      console.log(data);

      requestIsBeingProcessed = false;

    });
  }

  else
  {
    console.log("Wait until the current request is processed");
  }
}

// orders reload logic

function reloadOrders(paginationOptions)
{
  let formData = {
    "curPage": String(paginationOptions["curPage"]),
    "itemsToView": String(paginationOptions["itemsToView"]),
    "orderNum": String(filterOptions["orderNum"]),
    "ordersType": filterOptions["ordersType"]
  };

  requestOrders("POST", orderListAddress, formData);

  ordersPagination.innerHTML = "";

  renderPagination(paginationOptions, ordersPagination);
}

</script>

@endsection
