
@extends('layouts.shop')

@section('title', 'პაროლის დადასტურება')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">პაროლის დადასტურება</h2>
     <form id="password-confirmation-form" action="{{ route('shop.password.confirm') }}" autocomplete="off">
      <div class="mb-4">
        <label for="password-confirm" class="col-form-label font-7">შეიყვანეთ პაროლი</label>
        <input type="password" class="form-control shadow-none" name="password" maxlength="50" id="password-confirm">
      </div>
      <div class="mb-4">
       <div class="form-validation-errors" id="password-confirm-errors-block" style="display: none"></div>
      </div>
      <div class="button-row mt-4 mb-3">
        <button type="submit" class="btn btn-primary shadow-none font-5">პაროლის დადასტურება</button>
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

    let passwordConfirmationForm = document.getElementById("password-confirmation-form");
    let passwordConfirmationErrorsBlock = document.getElementById("password-confirm-errors-block");
    let passwordConfirmationRequestIsBeingProcessed = false;

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

    passwordConfirmationForm.addEventListener("submit", function(event){

        event.preventDefault();

        let numOfEmptyFields = controlEmptyInputs(this);
        let requestAddress = this.getAttribute("action");
        let requestMethod = "POST";

        if (numOfEmptyFields == 0 && !passwordConfirmationRequestIsBeingProcessed)
        {
          passwordConfirmationRequestIsBeingProcessed = true;

          let formData = new FormData(this);

          formData.append("_token", document.querySelector("meta[name=scrt]").getAttribute("content"));
        }
    });

})();

</script>

@endsection
