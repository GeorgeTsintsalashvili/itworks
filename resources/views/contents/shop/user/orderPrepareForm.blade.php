
@extends('layouts.shop')

@section('title', 'შეკვეთის გაფორმება')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-9">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">შეკვეთის დეტალები</h2>
     <form id="order-prepare-form" method="POST" action="{{ route('placeOrder') }}" autocomplete="off">
      <div class="form-row mb-4">
        <h6 class="form-row-heading">გაფრთხილება</h6>
        <div class="form-description mb-4">
          <i class="fas fa-exclamation-circle"></i>
          <span>თუ თქვენ ელოდებით მაღაზიისგან 5 შეკვეთის დადასტურებას, მაშინ უკვე მიღწეულია დაწესებული რაოდენობრივი ლიმიტი, რის გამოც მინიმუმ 1 შეკვეთის დადასტურებამდე შეგეზღუდებათ შეკვეთის დამატების შესაძლებლობა.</span>
        </div>
      </div>
      <div class="form-row mb-4 d-flex paired-fields">
        <div class="form-field">
         <label for="order-prepare-name-field" class="col-form-label font-7">შემკვეთის სახელი და გვარი</label>
         <input type="text" class="form-control shadow-none" name="name" value="{{ $contentData['shopUser'] -> name }}" maxlength="200" id="order-prepare-name-field">
        </div>
        <div class="form-field">
         <label for="order-prepare-phone-field" class="col-form-label font-7">შემკვეთის ტელეფონი</label>
         <input type="text" class="form-control shadow-none" name="phone" value="{{ $contentData['shopUser'] -> phone }}" maxlength="50" data-regex="^\+?\d{4,50}$" id="order-prepare-phone-field">
        </div>
      </div>
      <div class="form-row mb-4">
        <h6 class="form-row-heading">მიწოდების მეთოდი</h6>
        <div class="form-description mb-4">
          <i class="fas fa-exclamation-circle"></i>
          <span>თუ შეკვეთის ჯამური ფასი შეადგენს 500 ლარზე ნაკლებს და ამასთან ერთად გნებავთ ისარგებლოთ ჩვენი მიწოდების სერვისით, მაშინ ამ ფასს დაემატება მიწოდების საფასური 10 ლარი
          და ასევე აუცილებლად უნდა გქონდეთ შევსებული მისამართის ველი შეკვეთის განთავსების ფორმაში</span>
        </div>
        <div class="delivery-option-select cstm-select closed" tabindex="-2">
         <input type="hidden" name="delivery-method" class="cstm-parameter" id="delivery-method-input"/>
         <div class="cstm-selected-option">
          <span> გთხოვთ აირიჩიოთ თქვენთვის სასურველი მიწოდების მეთოდი </span>
         </div>
         <div class="cstm-options-container">
          <div class="cstm-option-item" data-option-value="0">
           <span>მაღაზიიდან გატანა (თბილისში უნდა მობრძანდეთ ვაჟა-ფშაველას გამზირის 78ა ნომერში)</span>
          </div>
          <div class="cstm-option-item" data-option-value="1">
           <span>ადგილზე მიწოდება მთელი საქართველოს მასშტაბით</span>
          </div>
         </div>
        </div>
        <div class="form-field d-none mt-2">
         <label for="order-prepare-address-field" class="col-form-label font-7">შემკვეთის მიწოდების მისამართი (შეგიძლიათ შეცვალოთ)</label>
         <input type="hidden" class="form-control shadow-none" name="delivery-address" value="{{ $contentData['shopUser'] -> address }}" maxlength="500" id="order-prepare-address-field">
        </div>
      </div>

      <div class="form-row mb-4">
        <h6 class="form-row-heading">გადახდის მეთოდი</h6>
        <div class="form-description mb-4">
          <i class="fas fa-exclamation-circle"></i>
          <span>
            ჩვენ საიტზე სასურველი პროდუქციის შეძენა შეგიძლიათ პლასტიკური ბარათის, ონლაინ განვადების და ასევე საბანკო გადარიცხვის მეშვეობით (ინვოისის საფუძველზე).
            ამჟამად შეგვიძლია შემოგთავაზოთ მხოლოდ საქართველოს ბანკის ონლაინ განვადება. მაღაზიის მხრიდან შეკვეთის დაკმაყოფილების შემდეგ ანგარიშსწორება უნდა მოხდეს
            24 საათის განმავლობაში, რომლის ამოწურვის შემდეგაც ავტომატურად მოხდება შეკვეთის გაუქმება და პროგრამულად დაიბლოკება გადახდის მიღება. გაითვალისწინეთ, რომ
            ონლაინ განვადებით სარგებლობისთვის აუცილებელია შეკვეთის ღირებულება შეადგენდეს მინიმუმ 500 ლარს.
          </span>
        </div>
        <div class="payment-option-select cstm-select closed" tabindex="-2">
         <input type="hidden" class="cstm-parameter"/>
         <div class="cstm-selected-option">
          <span> გთხოვთ აირიჩიოთ თქვენთვის სასურველი გადახდის მეთოდი </span>
         </div>
         <div class="cstm-options-container">
          <div class="cstm-option-item" data-option-value="card">
           <span>გადახდა პლასტიკური ბარათის მეშვეობით (Visa & Mastercard)</span>
          </div>
          <div class="cstm-option-item" data-option-value="installment">
           <span>შეძენა ონლაინ განვადების მეშვეობით</span>
          </div>
          <div class="cstm-option-item" data-option-value="invoice">
           <span>გადახდა ინვოისის საფუძველზე</span>
          </div>
         </div>
        </div>
        <div id="payment-options-block" class="d-none">
         <input type="hidden" name="payment-method" id="payment-method-input">
         <div class="payment-options-container">
           <div class="card-row payment-method-group d-none">
             @foreach($contentData['cardPaymentMethodOptions'] as $paymentOption)
             <div class="payment-group-item" data-payment-method-id="{{ $paymentOption -> id }}">
              <img src="/images/payment/{{ $paymentOption -> payment_method_logo }}" title="{{ $paymentOption -> payment_method_title }}"/>
             </div>
             @endforeach
           </div>
           <div class="installment-row payment-method-group d-none">
             @foreach($contentData['installmentPaymentMethodOptions'] as $paymentOption)
             <div class="payment-group-item" data-payment-method-id="{{ $paymentOption -> id }}">
              <img src="/images/payment/{{ $paymentOption -> payment_method_logo }}" title="{{ $paymentOption -> payment_method_title }}"/>
             </div>
             @endforeach
           </div>
           <div class="invoice-row payment-method-group d-none">
             @foreach($contentData['invoicePaymentMethodOptions'] as $paymentOption)
             <div class="payment-group-item" data-payment-method-id="{{ $paymentOption -> id }}">
              <img src="/images/payment/{{ $paymentOption -> payment_method_logo }}" title="{{ $paymentOption -> payment_method_title }}"/>
             </div>
             @endforeach
           </div>
         </div>
        </div>
      </div>

      <div class="form-row mb-4">
        <div class="order-summary">
          <div class="order-summary-item">
            <b>ჯამური ფასი</b>
            <span id="order-total-price" data-order-total-price="{{ $contentData['shoppingCart'] -> total_price }}">₾ {{ $contentData['shoppingCart'] -> total_price }}</span>
          </div>
          <div class="order-summary-item">
            <b>ფასდაკლება</b>
            <span>₾ {{ $contentData['shoppingCart'] -> total_discount }}</span>
          </div>
          <div class="order-summary-item">
            <b>პროდუქციის რაოდენობა</b>
            <span>{{ $contentData['shopUser'] -> cartItemsQuantity() }}</span>
          </div>
        </div>
      </div>
      <div class="mb-4">
       <div class="form-validation-errors" id="order-prepare-erros-block" style="display: none"></div>
      </div>
      <div class="button-row mt-4 mb-3">
        <button type="submit" class="btn btn-primary shadow-none font-5">შეკვეთის განთავსება</button>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

<style type="text/css">

.form-field {
  font-family: font-7;
}

.general-form .cstm-selected-option {
  font-size: 15px;
  font-family: 'font-6';
  color: #474747;
  padding: 14px;
}

.general-form .cstm-option-item:hover,
.general-form .cstm-option-item.cstm-active-option {
  background-color: #595959;
  color: #ffffff;
}

.general-form .cstm-option-item {
  color: #272727
}

.general-form .cstm-select.open .cstm-selected-option span::after,
.general-form .cstm-select.closed .cstm-selected-option span::after {
  position: absolute;
  right: 12px;
  top: 12px;
}

.general-form .cstm-options-container {
  margin-top: -1px;
  border-top: none;
}

.general-form .cstm-option-item::before {
  content: "\f101";
  font-family: "Font Awesome 5 Pro";
  font-weight: 900;
}

.form-row-heading {
  font-family: font-8;
  font-weight: bold;
  font-size: 16px;
  padding-bottom: 6px;
}

.form-row {
  justify-content: space-between;
}

.paired-fields .form-field {
  width: calc(50% - 20px);
}

.order-summary-item {
  display: flex;
  justify-content: space-between;
}

.order-summary-item b {
  margin-right: 10px;
  font-size: 16px;
  font-family: 'font-8';
}

.order-summary-item span {
  font-size: 18px;
  font-family: 'font-8';
  font-weight: 600;
}

.order-summary-item:not(:last-child) {
  border-bottom: 1px solid #d6d6d6;
}

.order-summary-item {
  padding: 14px;
}

.order-summary {
  border: 1px solid #d6d6d6;
  background-color: #f6f6f6;
}

.form-description i {
  font-size: 20px;
}

/* payment methods style */

#payment-options-block {
  margin-top: 10px;
  margin-bottom: 20px;
}

#payment-options-block .payment-group-item {
  margin-top: 10px;
  margin-right: 10px;
  position: relative;
  cursor: pointer;
}

#payment-options-block .payment-group-item img:hover {
  border-color: #929292;
}

#payment-options-block .payment-group-item img {
  width: 100%;
  height: 70px;
  padding: 10px;
  border: 1px solid #d0d0d0;
}

#payment-options-block .payment-group-item.selected-payment-option img {
  border-color: #929292;
}

</style>

<link rel="stylesheet" href="/css/select.css?v=35" type="text/css" media="all" />
<script type="text/javascript" src="/js/select.js?v=35"></script>

<script type="text/javascript">

(() => {

  let orderPlaceErrorsBlock = document.getElementById("order-prepare-erros-block");
  let orderPrepareForm = document.getElementById("order-prepare-form");
  let addressInputContainer = document.getElementById("order-prepare-address-field").parentNode;
  let orderTotalPriceElement = document.getElementById("order-total-price");
  let orderTotalPrice = Number(orderTotalPriceElement.getAttribute("data-order-total-price"));
  let paymentOptionsBlock = document.getElementById("payment-options-block");
  let paymentMethodInput = document.getElementById("payment-method-input");

  let requestIsBeingProcessed = false;
  let requestMethod = orderPrepareForm.getAttribute("method");
  let requestAddress = orderPrepareForm.getAttribute("action");

  let deliveryMethodSelect = new CustomSingleSelect(".delivery-option-select", function(){

      let selectedValue = Number(this.querySelector("input").value);

      if (selectedValue == 1)
      {
        addressInputContainer.classList.remove("d-none");

        addressInputContainer.querySelector("input").type = "text";

        orderTotalPriceElement.innerText = "₾ " + (orderTotalPrice + 10);
      }

      else if (!addressInputContainer.classList.contains("d-none"))
      {
        addressInputContainer.classList.add("d-none");

        addressInputContainer.querySelector("input").type = "hidden";

        orderTotalPriceElement.innerText = "₾ " + orderTotalPrice;
      }
  });

  let paymentMethodSelect = new CustomSingleSelect(".payment-option-select", function(){

      let selectedValue = this.querySelector("input").value;

      if (paymentOptionsBlock.classList.contains("d-none"))
      {
        paymentOptionsBlock.classList.remove("d-none");
      }

      let paymentGroups = document.querySelectorAll(".payment-method-group");

      paymentGroups.forEach((element) => {

         if (!element.classList.contains("d-none"))
         {
           element.classList.add("d-none");
           element.classList.remove("d-flex");
         }
      });

      let optionsGroup = document.querySelector(`.${selectedValue}-row`);

      optionsGroup.classList.remove("d-none");
      optionsGroup.classList.add("d-flex");

      let paymentOptionItems = optionsGroup.querySelectorAll(".payment-group-item");

      paymentOptionItems.forEach((paymentItem) => paymentItem.classList.remove("selected-payment-option"));

      paymentMethodInput.value = "";

      optionsGroup.querySelectorAll(".payment-group-item").forEach((element) => {

          element.addEventListener("click", function(){

             paymentOptionItems.forEach((paymentItem) => paymentItem.classList.remove("selected-payment-option"));

             paymentMethodInput.value = this.getAttribute("data-payment-method-id");

             this.classList.add("selected-payment-option");
          });
      });
  });

  orderPrepareForm.addEventListener("submit", function(event){

      event.preventDefault();

      let numOfEmptyFields = controlEmptyFields(this);
      let customEmptyFields = controlCustomEmptyFields(["payment-method-input", "delivery-method-input"]);

      if (!numOfEmptyFields && !customEmptyFields.length && !requestIsBeingProcessed)
      {
        orderPlaceErrorsBlock.style.display = "none";
        orderPlaceErrorsBlock.innerHTML = "";

        let filterRules = {
          "order-prepare-phone-field": /\s|\-/g
        };

        filterInputs(filterRules);

        let incorrectFormatFields = controlFieldFormat(this);

        if (incorrectFormatFields.length == 0)
        {
          let formData = new FormData(this);

          formData.append("_token", document.querySelector("meta[name=scrt]").getAttribute("content"));

          requestIsBeingProcessed = true;

          axios({
            method: requestMethod,
            url: requestAddress,
            data: formData
          })
          .then(function (response) {

            const data = response["data"];

            if (data["orderPlaced"])
            {
              window.location.href = `/shop/order/success/${data["orderId"]}`;
            }

            else if (data["pendingOrdersLimitReached"])
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "დასადასტურებელი შეკვეთების რაოდენობრივი ლიმიტი მიღწეულია, დაელოდეთ შეკვეთების დადასტურებას.";

              orderPlaceErrorsBlock.appendChild(errorRow);
              orderPlaceErrorsBlock.style.display = "block";
            }

            else if (!data["installmentAllowed"])
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "განვადებით სარგებლობისთვის შეკვეთის მინიმალური ფასი უნდა შეადგენდეს 500 ლარს, აირჩიეთ გადახდის სხვა მეთოდი.";

              orderPlaceErrorsBlock.appendChild(errorRow);
              orderPlaceErrorsBlock.style.display = "block";
            }

            else
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "შეკვეთის განთავსება არ შესრულდა";

              orderPlaceErrorsBlock.appendChild(errorRow);
              orderPlaceErrorsBlock.style.display = "block";
            }

            console.log(data);

            requestIsBeingProcessed = false;

          })
          .catch((error) => {

            let data = error.response["data"];

            if (data["tokenMismatch"])
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "შეკვეთის განთავსებისთვის საჭიროა გვერდის გადატვირთვა";

              orderPlaceErrorsBlock.appendChild(errorRow);
              orderPlaceErrorsBlock.style.display = "block";
            }

            else console.log(data);

            requestIsBeingProcessed = false;

          });
        }

        else
        {
          orderPlaceErrorsBlock.style.display = "none";
          orderPlaceErrorsBlock.innerHTML = "";

          let errorMessages = {
            "order-prepare-phone-field": "დაიცავით ტელეფონის ნომერის ფორმატი. ნომერი უნდა შეიცავდეს მინიმუმ 4 ციფრს, ამასთან ერთად ნებადართულია პირველ სიმბოლოდ პლიუსის ჩაწერა."
          };

          for (let index in incorrectFormatFields)
          {
            let errorRow = document.createElement("div");

            errorRow.setAttribute("class", "form-error-row");
            errorRow.innerText = errorMessages[incorrectFormatFields[index]];

            orderPlaceErrorsBlock.appendChild(errorRow);
            orderPlaceErrorsBlock.style.display = "block";
          }
        }
      }

      else
      {
        orderPlaceErrorsBlock.style.display = "none";
        orderPlaceErrorsBlock.innerHTML = "";

        if (numOfEmptyFields)
        {
          let errorRow = document.createElement("div");

          errorRow.setAttribute("class", "form-error-row");
          errorRow.innerText = "შეავსეთ სავალდებულო ველები";

          orderPlaceErrorsBlock.appendChild(errorRow);
          orderPlaceErrorsBlock.style.display = "block";
        }

        else if (requestIsBeingProcessed)
        {
          let errorRow = document.createElement("div");

          errorRow.setAttribute("class", "form-error-row");
          errorRow.innerText = "დაელოდეთ სერვერისგან პასუხს";

          orderPlaceErrorsBlock.appendChild(errorRow);
          orderPlaceErrorsBlock.style.display = "block";
        }

        else
        {
          let errorMessages = {
            "delivery-method-input": "აირჩიეთ მიწოდების მეთოდი",
            "payment-method-input": "აირჩიეთ გადახდის მეთოდი"
          };

          for (let index in customEmptyFields)
          {
            let errorRow = document.createElement("div");

            errorRow.setAttribute("class", "form-error-row");
            errorRow.innerText = errorMessages[customEmptyFields[index]];

            orderPlaceErrorsBlock.appendChild(errorRow);
            orderPlaceErrorsBlock.style.display = "block";
          }
        }
      }
  });

})();

</script>

@endsection
