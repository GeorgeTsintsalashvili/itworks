
@extends('layouts.controlPanelAuth')

@section('title', 'პაროლის აღდგენა')

@section('content')

<div class="content-wrapper">
 <section id="form-section">
  <div class="form-wrapper">
     <form id="email-form" method="POST" action="{{ route('password.email') }}" autocomplete="off">
       <div class="company-logo">
         <a href="/" target="_blank">
          <img src="/images/general/logo.png">
         </a>
        </div>
       <div class="input-wrapper">
        <i class="fas fa-user" aria-hidden="true"></i>
        <input type="text" class="form-input font-8" name="email" placeholder="შეიყვანეთ ელ. ფოსტა"/>
       </div>
       <div class="errors-wrapper hidden"></div>
       <div class="control-wrapper">
        <a href="{{ route('login') }}">
         <i class="fas fa-sign-in-alt"></i>
         <span class="font-6"> ავტორიზაციის გვერდი </span>
        </a>
       </div>
       <div class="submit-button-wrapper">
        <input class="submit-button font-6" type="submit" value="აღდგენის ბმულის გაგზავნა">
       </div>
     </form>
  </div>
 </section>

</div>

<!--- form processing script --->

<script type="text/javascript">

((formSelector) => {

  let emailForm = document.getElementById(formSelector);
  let errorsWrapper = document.querySelector(".errors-wrapper");
  let requestIsBeingProcessed = false;

  emailForm.addEventListener("submit", function(event) {

    event.preventDefault();

    if (!requestIsBeingProcessed)
    {
      let numOfEmptyFields = controlEmptyFields(this);

      if (numOfEmptyFields == 0)
      {
        let formData = new FormData(this);
        let requestAddress = this.getAttribute("action");
        let requestMethod = this.getAttribute("method");

        requestIsBeingProcessed = true;

        formData.append("_token", document.querySelector("meta[name=csrftkn]").getAttribute("content"));

        axios({
          method: requestMethod,
          url: requestAddress,
          data: formData
        })
        .then(function(response) {

          let successText = "პაროლის აღდგენის ლინკი გაიგზავნა";

          if (Array.isArray(response["data"]))
          {
            if (!errorsWrapper.classList.contains("hidden"))
            {
              errorsWrapper.classList.add("hidden");
            }

            errorsWrapper.innerHTML = "";

            emailForm.reset();
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

})("email-form");

</script>

@endsection
