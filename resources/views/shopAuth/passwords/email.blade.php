
@extends('layouts.controlPanelAuth')

@section('title', 'პაროლის აღდგენა')

@section('content')

<link href="/admin/css/snackbar.css" rel="stylesheet">

<div class="content-wrapper">

 <section id="form-section">
  <div class="form-wrapper">
     <form id="email-form" method="POST" action="{{ route('shop.password.email') }}" autocomplete="off">
       <div class="company-logo">
         <a href="/" target="_blank">
          <img src="/images/general/logo.png">
         </a>
        </div>

       <div class="input-wrapper">
         <i class="fa fa-envelope" aria-hidden="true"></i>
         <input type="text" class="form-input font-3" name="email" placeholder="შეიყვანეთ ელ. ფოსტა" value="{{ old('email') }}"/>
       </div>

       <div class="input-wrapper">
        <a id="auth-back" href="{{ route('shop.login') }}">
           <i class="fas fa-sign-in-alt"></i>
           <span class="font-3"> ავტორიზაციის გვერდი </span>
        </a>
       </div>

       <div class="submit-button-wrapper">
         <input class="submit-button font-3" type="submit" value="აღდგენის ბმულის გაგზავნა">
       </div>

     </form>
  </div>
 </section>

</div>

<!--- snackbar element to display request response --->

<div id="snackbar">
 <i class="fa fa-info-circle"></i>
 <span class="message font-3"></span>
</div>

@include('parts.controlPanel.general')

<!--- Form processing script --->

<script type="text/javascript">

function submitHandler(e)
{
   e.preventDefault();

   let successCallback = (data) => {

       let message = "პაროლის აღდგენის ლინკი გაიგზავნა"
       let errorCode = data["errorCode"];

       switch(errorCode)
       {
         case 0: $(this).trigger("reset"); break;
         case 1: message = "იმეილის ფორმატი არაა სწორი ან მომხმარებელი ვერ მოიძებნა"; break;
         case 2: message = "პაროლის აღდგენის ლინკი ვერ გაიგზავნა"; break;
       }

       printMessage(message);
   }

   let data = $(this).serialize();
   let method = $(this).attr("method");
   let address = $(this).attr("action");

   sendRequest(method, address, data, successCallback);
}

$("#email-form").submit(submitHandler);

</script>

@endsection
