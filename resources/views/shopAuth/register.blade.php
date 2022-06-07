
@extends('layouts.shop')

@section('title', 'სტანდარტული ფორმა')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">სტანდარტული ფორმა</h2>
     <form id="general-form" method="" action="" autocomplete="off">
      <div class="mb-4">
        <label for="general-form-field" class="col-form-label font-7">სტანდარტული ველი</label>
        <input type="text" class="form-control shadow-none" name="field" value="" id="general-form-field">
      </div>
      <div class="mb-4">
       <div class="form-validation-errors" id="general-validation-erros-block" style="display: none"></div>
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

@endsection
