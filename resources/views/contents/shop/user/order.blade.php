
<!DOCTYPE html>

<html lang="ka">
 <head>
  <title>შეკვეთა #{{ $contentData['order'] -> id }}</title>
  <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <link rel="icon" href="/images/general/logo.ico" />
  <!--- styles --->
  <link rel="stylesheet" href="/fonts/various/css/fonts.css?v=34" type="text/css" media="all" />
  <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css?v=34" type="text/css" media="all" />
 </head>
 <body>

  <header class="mt-4">
    <div class="container">
      <div class="d-flex company-info-row">
        <div class="company-info-col logo-col">
          <img src="/images/general/logo.png">
        </div>
      </div>
    </div>
  </header>

  <section id="order-section" class="mt-4">
   <div class="container">
     <div class="row">
       <div class="col-sm-12">
         <div id="order-block">
          <h5 class="order-title">შეკვეთის დეტალები</h5>
          <div class="order-item">
           <div class="order-row">
            <div class="order-info-key">
             <b>შეკვეთის აიდი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> id }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>შემკვეთის სახელი და გვარი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> customer_name }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>შემკვეთის ტელეფონი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> customer_phone }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>განთავსების თარიღი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> order_placement_date }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>შეკვეთის ღირებულება</b>
            </div>
            <div class="order-info-value">
             <span>₾ {{ $contentData['order'] -> order_price }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>მიწოდების მეთოდი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> deliver ? "ადგილზე მიწოდება" : "მაღაზიიდან გატანა" }}</span>
            </div>
           </div>
           @if($contentData['order'] -> delivery_address)
           <div class="order-row">
            <div class="order-info-key">
             <b>მიწოდების მისამართი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> delivery_address }}</span>
            </div>
           </div>
           @endif
           <div class="order-row">
            <div class="order-info-key">
             <b>გადახდის მეთოდი</b>
            </div>
            <div class="order-info-value">
             <span>{{ $contentData['order'] -> payment_method_title }}</span>
            </div>
           </div>
           <div class="order-row">
            <div class="order-info-key">
             <b>შეკვეთის სტატუსი</b>
            </div>
            <div class="order-info-value">
             <span style="color: {{ $contentData['order'] -> order_status_color }}">{{ $contentData['order'] -> order_status_title }}</span>
            </div>
           </div>
          </div>
         </div>
         <div class="order-products-list">
           <h5 class="order-title">არჩეული პროდუქცია</h5>
           @foreach($contentData['order'] -> order_items as $orderItem)
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
     </div>
   </div>
 </section>

 <style type="text/css">

 /* header style */

 header .label {
   font-family: font-6;
   font-size: 16px;
 }

 .logo-col img {
   width: 400px;
 }

 .company-info-row {
   justify-content: space-around;
   align-items: center;
 }

 /* order style */

 #order-block .order-item {
   border: 1px solid #a8a8a8;
   margin-bottom: 50px;
   background-color: #fafafa;
 }

 #order-block .order-row {
   display: flex;
   justify-content: space-between;
   padding: 10px;
 }

 #order-block .order-row:not(:last-child) {
   border-bottom: 1px solid #d4d4d4;
 }

 #order-block .order-item {
   position: relative;
 }

 #order-block .order-item:not(:last-child)::after {
   content: "";
   border-top: 3px solid #0a756a;
   width: 100px;
   position: absolute;
   bottom: -26px;
   left: calc(50% - 50px);
 }

 #order-block .order-item:not(:last-child)::before {
   content: "";
   border-top: 1px solid #0a756a;
   width: 300px;
   position: absolute;
   bottom: -25px;
   left: calc(50% - 150px);
 }

 #order-block .order-info-key {
   margin-right: 10px;
   font-size: 14px;
   font-family: font-8;
 }

 #order-block .order-info-value {
   font-size: 14px;
   font-family: font-7;
 }

 #order-quantity {
   color: #0a756a;
 }

 /* order item style */

 .order-title {
   font-family: font-8;
   font-size: 20px;
   margin-top: 20px;
   margin-bottom: 20px;
 }

 .order-products-list {
   margin-top: 10px;
 }

 .order-item-row:first-child {
   margin-top: 20px;
 }

 .order-item-row {
   padding: 12px;
   display: flex;
   justify-content: space-between;
   border: 1px solid #d4d4d4;
   background-color: #fafafa;
   margin-bottom: 20px;
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

 @media not print {

   @media (max-width: 768px) {

     .order-item-row img {
       width: 100%;
     }

     .order-item-col:not(:last-child),
     .order-item-col:last-child {
       margin: 20px auto;
     }

     .order-item-row {
       flex-direction: column;
     }

     .order-item-col {
       width: 60%;
       text-align: center;
     }
   }
 }

 .order-info-key {
   margin-right: 10px;
   font-size: 14px;
   font-family: font-8;
   width: 50%;
 }

 .order-info-value {
   font-size: 14px;
   font-family: font-7;
   width: 50%;
   text-align: right;
 }

 </style>

 </body>
</html>
