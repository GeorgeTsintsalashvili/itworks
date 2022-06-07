
@extends('layouts.shop')

@section('title', 'ელექტრონული ფოსტის წარმატებული ვერიფიკაცია')

@section('content')

<!-- delimiter start -->

<div class="general-form-page-delimiter"></div>

<!-- delimiter end -->

<section class="form-section">
 <div class="container">
  <div class="row justify-content-center">
   <div class="col-sm-6">
    <div class="general-form">
     <h2 class="font-6 mb-4 form-heading">წარმატებული ვერიფიკაცია</h2>
     <form autocomplete="off">
      <div class="mb-4">
       <div class="form-description">
         <span>
          თქვენი ელექტრონული ფოსტის მისამართის ვერიფიკაცია შესრულდა წარმატებულად, რაც გაძლევთ საშუალებას ისარგებლოთ ჩვენი საიტის სერვისებით.
          გაითვალისწინეთ, რომ ვერ შეძლებთ ფიქტიური ელექტრონული ფოსტების მისამართებით რეგისტრაციის გავლას. თუ ვერიფიკაციის გავლის შემდეგ, ან ვერიფიკაციამდე მოხდა
          თქვენი ელექტრონული ფოსტის გაუქმება, მაშინ ვეღარ შეძლებთ დავიწყებული პაროლის აღდგენას და ვერც ვერიფიკაციის ახალი ლინკის მიღებას, რადგან პაროლის აღდგენისა
          და იმეილის ვერიფიკაციის ბმულები იგზავნება მხოლოდ მოქმედ ელექტრონული ფოსტის მისამართზე.
         </span>
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
 </div>
</section>

@endsection
