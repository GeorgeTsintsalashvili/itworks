
@extends('layouts.controlPanelAuth')

@section('title', 'რეგისტრაციის ფორმა')

@section('content')

<div class="content-wrapper">

<section id="form-section">
 <div class="form-wrapper">
     <form id="register-form" method="POST" action="{{ route('register') }}" autocomplete="off">
       @csrf
       <div class="company-logo">
         <a href="/" target="_blank">
          <img src="/images/general/logo.png">
         </a>
        </div>

        <div class="input-wrapper">
          <i class="fa fa-user" aria-hidden="true"></i>
          <input type="text" class="form-input font-6" name="name" placeholder="სახელი"/>
        </div>

       <div class="input-wrapper">
         <i class="fa fa-envelope" aria-hidden="true"></i>
         <input type="text" class="form-input font-6" name="email" placeholder="ელ. ფოსტა"/>
       </div>

        <div class="input-wrapper">
          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" class="form-input font-6" name="password" placeholder="პაროლი"/>
        </div>

        <div class="input-wrapper">
          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" class="form-input font-6" name="password_confirmation" placeholder="გაიმეორეთ პაროლი"/>
        </div>

        @if($errors->any())
            {!! implode('', $errors->all('<div>:message</div>')) !!}
        @endif

        <div class="input-wrapper">
         <a id="auth-back" href="{{ route('login') }}">
            <i class="fas fa-sign-in-alt"></i>
            <span class="font-6"> ავტორიზაციის გვერდი </span>
         </a>
       </div>

       <div class="submit-button-wrapper">
         <input class="submit-button font-6" type="submit" value="დარეგისტრირება" name="register">
       </div>

     </form>
 </div>
</section>

</div>

@endsection
