
@extends('layouts.shop')

@section('title', 'პაროლის აღდგენა')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">პაროლის გადაყენება</h2>
     <form id="forgotten-password-reset-form" action="{{ route('shop.password.update') }}" autocomplete="off">
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="mb-4">
        <label for="reset-form-email" class="col-form-label font-7">ელ. ფოსტა</label>
        <input type="text" class="form-control shadow-none" name="email" value="{{ $email }}" maxlength="100" id="reset-form-email">
      </div>
      <div class="mb-4">
        <label for="reset-form-password" class="col-form-label font-7">ახალი პაროლი (მინიმუმ 8 სიმბოლო)</label>
        <div class="input-wrapper">
          <input type="password" class="form-control shadow-none" name="password" maxlength="50" id="reset-form-password">
          <span class="password-visibility">
            <i class="fas fa-eye-slash"></i>
          </span>
        </div>
      </div>
      <div class="mb-4">
        <label for="reset-form-password-confirmation" class="col-form-label font-7">გაიმეორეთ პაროლი</label>
        <div class="input-wrapper">
          <input type="password" class="form-control shadow-none" name="password_confirmation" maxlength="50" id="reset-form-password-confirmation">
          <span class="password-visibility">
            <i class="fas fa-eye-slash"></i>
          </span>
        </div>
      </div>

      <div class="mb-4">
       <div class="form-validation-errors" id="password-reset-erros-block" style="display: none"></div>
      </div>

      <div class="button-row mt-4 mb-3">
        <button type="submit" class="btn btn-primary shadow-none font-5">მონაცემების გაგზავნა</button>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

<!--- form processing logic --->

<script type="text/javascript">

(() => {

    let forgottenPasswordResetForm = document.getElementById("forgotten-password-reset-form");
    let passwordResetErrosBlock = document.getElementById("password-reset-erros-block");
    let resetRequestIsBeingProcessed = false;

    function controlEmptyInputs(form)
    {
      let numOfEmptyFields = 0;

      let inputs = form.querySelectorAll("input:not([type=hidden])");

      inputs.forEach((element) => {

        element.value = element.value.trim();

        if (!element.classList.contains("optional-field"))
        {
          if (element.classList.contains("form-invalid-field-color"))
          {
            element.classList.remove("form-invalid-field-color");
          }

          if (element.value.length == 0)
          {
            numOfEmptyFields++;

            element.classList.add("form-invalid-field-color");
          }
        }

      });

      return numOfEmptyFields;
    }

    forgottenPasswordResetForm.addEventListener("submit", function(event){

        event.preventDefault();

        let numOfEmptyFields = controlEmptyInputs(this);
        let requestAddress = this.getAttribute("action");
        let requestMethod = "POST";

        if (numOfEmptyFields == 0 && !resetRequestIsBeingProcessed)
        {
          requestIsBeingProcessed = true;

          let formData = new FormData(this);

          formData.append("_token", document.querySelector("meta[name=scrt]").getAttribute("content"));

          axios({
            method: requestMethod,
            url: requestAddress,
            data: formData
          })
          .then(function (response) {

            if (response["data"]["passwordChanged"])
            {
              forgottenPasswordResetForm.reset();

              passwordResetErrosBlock.innerHTML = "";
              passwordResetErrosBlock.style.display = "none";
              window.location.href = response["data"]["redirect"];
            }

            requestIsBeingProcessed = false;

          })
          .catch((error) => {

            passwordResetErrosBlock.innerHTML = "";

            if (error.response["data"]["tokenMismatch"])
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "გადატვირთეთ გვერდი და თავიდან შეიყვანეთ მონაცემები";
              passwordResetErrosBlock.appendChild(errorRow);
              passwordResetErrosBlock.style.display = "block";
            }

            else if (error.response["data"]["errors"])
            {
              passwordResetErrosBlock.innerHTML = "";

              let validationErrors = error.response["data"]["errors"];

              for (let errorKey in validationErrors)
              {
                let errorsArray = validationErrors[errorKey];

                for (let index in errorsArray)
                {
                  let errorRow = document.createElement("div");

                  errorRow.setAttribute("class", "form-error-row");
                  errorRow.innerText = errorsArray[index];
                  passwordResetErrosBlock.appendChild(errorRow);
                }
              }

              passwordResetErrosBlock.style.display = "block";
            }

            else
            {
              let errorRow = document.createElement("div");

              errorRow.setAttribute("class", "form-error-row");
              errorRow.innerText = "პაროლის შეცვლა ვერ მოხერხდა";
              passwordResetErrosBlock.appendChild(errorRow);
              passwordResetErrosBlock.style.display = "block";
            }

            requestIsBeingProcessed = false;

          });
        }
    });

})();

</script>

@endsection
