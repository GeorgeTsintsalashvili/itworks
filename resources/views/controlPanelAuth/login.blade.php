@extends('layouts.controlPanelAuth')
@section('title', 'ავტორიზაციის ფორმა')
@section('content')

<div class="content-wrapper">
  <section id="form-section">
    <div class="form-wrapper">
      <form id="login-form" method="POST" action="{{ route('login') }}" autocomplete="off" data-redirect="{{ route('controlPanelHome') }}">
        <div class="company-logo">
         <a href="/" target="_blank">
          <img src="/images/general/logo.png">
         </a>
        </div>
        <div class="input-wrapper">
         <i class="fas fa-user" aria-hidden="true"></i>
         <input type="text" class="form-input font-8" name="email" placeholder="ელ. ფოსტა"/>
        </div>
        <div class="input-wrapper">
         <i class="fas fa-lock" aria-hidden="true"></i>
         <input type="password" class="form-input font-8" name="password" placeholder="პაროლი"/>
        </div>
        <div class="errors-wrapper hidden"></div>
        <div class="control-wrapper">
         <div class="custom-checkbox remember">
          <span class="custom-checkbox-square unchecked"></span>
          <span class="custom-checkbox-text font-6 noselect">დამახსოვრება</span>
          <input type="hidden" name="remember" value="0">
         </div>
        </div>
        <div class="control-wrapper">
         <a href="{{ route('password.request') }}">
          <i class="fas fa-undo"></i>
          <span class="font-6"> პაროლის აღდგენა </span>
         </a>
        </div>
        <div class="submit-button-wrapper">
         <input class="submit-button font-6" type="submit" value="სისტემაში შესვლა" name="login">
        </div>
      </form>
    </div>
  </section>
</div>

<!--- form processing script --->
<script type="text/javascript">

// single checkbox control logic

function checkboxControl(selector) {
  let customCheckbox = document.querySelector(selector);
  let input = customCheckbox.querySelector("input");
  let checkboxSquare = customCheckbox.querySelector(".custom-checkbox-square");

  customCheckbox.addEventListener("click", () => {

    input.value = Number(!parseInt(input.value));

    if (checkboxSquare.classList.contains("unchecked")) {
      checkboxSquare.classList.add("checked");
      checkboxSquare.classList.remove("unchecked");
    } else {
      checkboxSquare.classList.add("unchecked");
      checkboxSquare.classList.remove("checked");
    }
  });
}

checkboxControl(".custom-checkbox.remember");

((formSelector) => {

  let loginForm = document.getElementById(formSelector);
  let errorsWrapper = document.querySelector(".errors-wrapper");
  let requestIsBeingProcessed = false;

  loginForm.addEventListener("submit", function(event) {

    event.preventDefault();

    if (!requestIsBeingProcessed) {
      let numOfEmptyFields = controlEmptyFields(this);

      if (numOfEmptyFields == 0) {

        let requestAddress = this.getAttribute("action");
        let requestMethod = this.getAttribute("method");
        let redirectAddress = this.getAttribute("data-redirect");

        requestIsBeingProcessed = true;

        let formData = new FormData(this);

        formData.append("_token", document.querySelector("meta[name=csrftkn]").getAttribute("content"));

        axios({
          method: requestMethod,
          url: requestAddress,
          data: formData
        })
        .then(function(response) {

          if (Array.isArray(response["data"]))
          {
            if (!errorsWrapper.classList.contains("hidden"))
            {
              errorsWrapper.classList.add("hidden");
            }

            window.location.href = redirectAddress;
          }

          else
          {
            if (errorsWrapper.classList.contains("hidden"))
            {
              errorsWrapper.classList.remove("hidden");
            }

            errorsWrapper.innerHTML = "";

            for (let key in response["data"])
            {
              response["data"][key].forEach((errorText) => {
                let errorItem = document.createElement("div");
                errorItem.setAttribute("class", "error-item");
                errorItem.innerText = errorText;
                errorsWrapper.appendChild(errorItem);
              });
            }
          }

          requestIsBeingProcessed = false;

        })
        .catch((error) => {

          errorsWrapper.classList.add("hidden");
          errorsWrapper.innerHTML = "";

          console.log(error.response["data"]);

          requestIsBeingProcessed = false;

        });
      }

      else
      {
        errorsWrapper.innerHTML = "";

        let errorItem = document.createElement("div");

        errorItem.setAttribute("class", "error-item");
        errorItem.innerText = "შეავსეთ სავალდებულო ველები";
        errorsWrapper.appendChild(errorItem);

        if (errorsWrapper.classList.contains("hidden"))
        {
          errorsWrapper.classList.remove("hidden");
        }
      }
    }

    else
    {
      errorsWrapper.innerHTML = "";

      let errorItem = document.createElement("div");

      errorItem.setAttribute("class", "error-item");
      errorItem.innerText = "დაელოდეთ სერვერსიგან პასუხს";
      errorsWrapper.appendChild(errorItem);

      if (errorsWrapper.classList.contains("hidden"))
      {
        errorsWrapper.classList.remove("hidden");
      }
     }
   });

  })("login-form");

</script>
@endsection
