
@extends('layouts.controlPanelAuth')

@section('title', 'პაროლის დადასტურება')

@section('content')

<div class="content-wrapper">

 <section id="form-section">
  <div class="form-wrapper">
     <form id="confirm-form" method="POST" action="{{ route('password.confirm') }}" autocomplete="off">
       @csrf

       <div class="company-logo">
         <a href="/" target="_blank">
          <img src="/images/general/logo.png">
         </a>
        </div>

        <div class="input-wrapper">
          <i class="fa fa-lock" aria-hidden="true"></i>
          <input type="password" class="form-input font-8" name="password" placeholder="პაროლი"/>
        </div>

       <div class="submit-button-wrapper">
         <input class="submit-button font-6" type="submit" value="დადასტურება" name="confirm">
       </div>

     </form>
  </div>
 </section>

</div>

@endsection
