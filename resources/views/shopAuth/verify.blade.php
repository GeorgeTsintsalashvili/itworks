
@extends('layouts.shop')

@section('title', 'ელექტრონული ფოსტის დადასტურება')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">ელექტრონული ფოსტის ვერიფიკაცია</h2>
     <form id="email-verification-form" method="GET" action="{{ route('shop.verification.resend') }}" autocomplete="off">

      <div class="mb-4">
       <div class="form-description">
         <span>
           გთხოვთ დაადასტუროთ თქვენი <b>{{ Auth::guard('shopUser') -> user() -> email }}</b> მისამართის სინამდვილე, წინაამღდეგ შემთხვევაში ავტომატურად მოხდება ანგარიშის დახურვა.
           თუ ელექტრონულ ფოსტაზე ტექნიკური ხარვეზების გამო არ მოგივიდათ ვერიფიკაციის ბმული, მაშინ შეგიძლიათ ისარგებლოთ ამ ფორმით, რათა ხელახლა შეძლოთ ბმულის გაგზავნა. ვერიფიკაციის წარმატებით გავლის
           შემდეგ რეგისტრაცია ჩაითვლება დასრულებულად და შემდგომ უკვე შეგეძლებათ საიტის სერვისებით სარგებლობა. გაითვალისწინეთ, რომ ვერიფიკაციის ბმულის მოქმედების ვადა არის 60 წუთი, რომლის ამოწურვის
           შემდეგ მოგიწევთ ბმულის განმეორებით გაგზავნა.
         </span>
       </div>
      </div>

      <div class="mb-4 mt-4">
       <div class="form-validation-errors" id="email-verification-errors-block" style="display: none"></div>
      </div>

      <div class="button-row mt-4 mb-3">
        <button type="submit" class="btn btn-primary shadow-none font-5">ვერიფიკაციის ბმულის გაგზავნა</button>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

<script type="text/javascript">

(() => {

  let verificationForm = document.getElementById("email-verification-form");
  let emailVerificationErrorsBlock = document.getElementById("email-verification-errors-block");
  let resendRequestIsBeingProcessed = false;

  let requestAddress = verificationForm.getAttribute("action");
  let requestMethod = verificationForm.getAttribute("method");

  verificationForm.addEventListener("submit", function(event){

      event.preventDefault();

      if (!resendRequestIsBeingProcessed)
      {
        resendRequestIsBeingProcessed = true;

        axios({
          method: requestMethod,
          url: requestAddress
        })
        .then(function (response) {

          emailVerificationErrorsBlock.style.display = "none";
          emailVerificationErrorsBlock.innerHTML = "";

          if (response["data"]["verificationLinkIsResent"])
          {
            let successRow = document.createElement("div");

            successRow.setAttribute("class", "form-success-row");
            successRow.innerText = "ვერიფიკაციის ლინკი გაიგზავნა";

            emailVerificationErrorsBlock.appendChild(successRow);
            emailVerificationErrorsBlock.style.display = "block";
          }

          else if (response["data"]["emailAlreadyVerified"])
          {
            let successRow = document.createElement("div");

            successRow.setAttribute("class", "form-success-row");
            successRow.innerText = "იმეილი უკვე დადასტურებულია";

            emailVerificationErrorsBlock.appendChild(successRow);
            emailVerificationErrorsBlock.style.display = "block";
          }

          else
          {
            window.location.href = "/#authorization-modal";
          }

          resendRequestIsBeingProcessed = false;

        })
        .catch((error) => {

          emailVerificationErrorsBlock.style.display = "none";
          emailVerificationErrorsBlock.innerHTML = "";

          if (error.response["data"]["mailServerFault"])
          {
            let errorRow = document.createElement("div");

            errorRow.setAttribute("class", "form-error-row");
            errorRow.innerText = "თქვენი ელექტრონული ფოსტა არის მიუწვდომელი";

            emailVerificationErrorsBlock.appendChild(errorRow);
            emailVerificationErrorsBlock.style.display = "block";
          }

          else if (error.response["data"]["tooManyRequests"])
          {
            let errorRow = document.createElement("div");

            errorRow.setAttribute("class", "form-error-row");
            errorRow.innerText = "დროის მოკლე ინტერვალში ვერიფიკაციის ლინკის გაგზავნის მრავალჯერადი მცდელობის გამო თქვენი მოთხოვნა დაიბლოკა";

            emailVerificationErrorsBlock.appendChild(errorRow);
            emailVerificationErrorsBlock.style.display = "block";
          }

          resendRequestIsBeingProcessed = false;

        });
      }

      else
      {
        emailVerificationErrorsBlock.style.display = "none";
        emailVerificationErrorsBlock.innerHTML = "";

        let errorRow = document.createElement("div");

        errorRow.setAttribute("class", "form-error-row");
        errorRow.innerText = "დაელოდეთ სერვერისგან პასუხს";

        emailVerificationErrorsBlock.appendChild(errorRow);
        emailVerificationErrorsBlock.style.display = "block";
      }
  });

})();

</script>

@endsection
