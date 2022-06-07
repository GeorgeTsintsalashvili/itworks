<!doctype html>

<html>
 <head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="/admin/plugins/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/admin/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="/admin/plugins/bootstrap/css/bootstrap-grid.css">
  <link rel="stylesheet" href="/admin/plugins/bootstrap/css/bootstrap-grid.min.css">
 <head>
<body>

<header>
 <div class="container-fluid mt-3">
   <div class="row">
     <div class="col-md-4">
       <img src="/images/general/logo.png" width="100%">
     </div>
     <div class="col-md-8">
      <ul class="float-right">
        <li>
          <b class="font-6">ტელეფონი: </b>
          <span> {{ $contact -> phone }} </span>
        </li>
        <li>
          <b class="font-6">ელ. ფოსტა: </b>
          <span> {{ $contact -> email }} </span>
        </li>
        <li>
          <b class="font-6"> მისამართი: </b>
          <span> {{ $contact -> address }} </span>
        </li>
        <li>
          <b class="font-6"> საიტის მისამართი: </b>
          <span> https:://www.itworks.ge  </span>
        </li>
      </ul>
     </div>
   </div>
 </div>
</header>

<main class="mt-3">
  <div class="container-fluid">

   <div class="row">
    <div class="col-md-12">
      <div class="warranty-rules-container">

       <h3 class="rules-title font-6 text-center"> საგარანტიო ფურცელი </h3>

       <p class="warranty-rules font-6">
         საგარანტიო პირობების თანახმად, შეძენილი ნივთის მწყობრიდან გამოსვლის შემთხვევაში, გამყიდველი ვალდებულებას იღებს
         მის შეკეთებაზე(ნაწილის დაზიანბის შემთხვევაში ხდება იგივე მწარმოებლის ანალოგიური მოდელის ნაწილით შეცვლა),
         სერვის-ცენტრში მოტანიდან არაუგვიანეს 10 სამუშაო დღისა. გაყიდული ნივთი საგარანტიოდან მოიხსნება ნებისმიერი
         ფიზიკური დაზიანების შემთხვევაში (ასევე, კვების ბლოკზე ან მისით გამოწვეულ დაზიანებებზე) საგარანტიო პირობები
         არ ვრცელდება პროგრამულ უზრუნველყოფაზე.
       </p>

      </div>
    </div>
   </div>

    <div class="row mt-3">
      <div class="col-md-12">
        <table class="table">
          <thead>
            <tr class="font-6">
              <th scope="col">#</th>
              <th scope="col">დასახელება</th>
              <th scope="col" class="text-center">რაოდენობა</th>
              <th scope="col" class="text-center">გარანტია</th>
              <th scope="col" class="text-center">სულ ფასი</th>
            </tr>
          </thead>

          <tbody>

            @foreach($systemComponents as $index => $product)

            <tr>
              <th scope="row">{{ $index + 1 }}</th>
              <td>
                <span class="label">{{ $product['title'] }}</span>
              </td>

              <td class="text-center">
                <span class="label">{{ $product['quantity'] }}</span>
              </td>

              @if($index == 0)

              <td class="text-center align-middle" rowspan="{{ $numOfSystemComponents }}">
                <span class="label">1 წელი</span>
              </td>

              <td class="text-center align-middle" rowspan="{{ $numOfSystemComponents }}">
                <span class="label">{{ $systemPrice }}</span>
                <b class="price"> ₾ </b>
              </td>

              @endif

            </tr>

            @endforeach


            @foreach($products as $key => $product)
            <tr>
              <th scope="row">{{ $key + 1 }}</th>
              <td>
                <span class="label">{{ $product['title'] }}</span>
              </td>
              <td class="text-center">
                <span class="label">{{ $product['quantity'] }}</span>
              </td>
              <td class="text-center">
                <span class="label">{{ $product['warranty'] }}</span>
              </td>
              <td class="text-center">
                <span class="label">{{ $product['price'] }}</span>
                <b class="price"> ₾ </b>
              </td>
            </tr>
            @endforeach


          </tbody>
        </table>
      </div>
    </div>

    <div class="row mt-3">
     <div class="col-md-6">
       <span class="total-price-title">სრული თანხა</span>
       <b class="total-price"> {{ $totalPrice }} </b>
       <b class="currency"> ₾ </b>
     </div>

     <div class="col-md-6 text-right">
       <span class="date-title">გაყიდვის თარიღი</span>
       <b class="date"> {{ date('d/m/Y') }} </b>
     </div>
    </div>

  </div>
</main>

<footer class="mt-5 mb-5">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-6">
         <b class="merchant-id-title"> შემსყიდველის პ/ნ </b>
         <span class="merchant-id"> {{ $clientId }} </span>
      </div>

      <div class="col-md-6">
        <b class="merachnt-siganture-title"> ხელმოწერა </b>
        <span class="merchant-siganture"> __________________ </span>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-6">
         <b class="manager-title"> მომსახურე მენეჯერი </b>
         <span class="manager"> {{ $manager }} </span>
      </div>

      <div class="col-md-6">
        <b class="manager-siganture-title"> ხელმოწერა </b>
        <span class="manager-siganture"> __________________ </span>
      </div>
    </div>

  </div>
</footer>

<!--- page style --->

<style type="text/css">

/* import fonts */

@font-face {
  font-family: font-1;
  src: url("/admin/fonts/various/bpg_glaho.ttf");
}

@font-face {
  font-family: font-2;
  src: url("/admin/fonts/various/bpg_nino_mtavruli_normal.ttf");
}

@font-face {
  font-family: font-3;
  src: url("/admin/fonts/various/bpg_nino_mtavruli_bold.ttf");
}

@font-face {
  font-family: font-6;
  src: url("/admin/fonts/various/bpg_mrgvlovani_2009.ttf");
}

.font-1{
  font-family: font-1;
}

.font-2{
  font-family: font-2;
}

.font-3{
  font-family: font-3;
}

.font-6{
  font-family: font-6;
}

/* primary style */

ul{
   list-style-type: none;
   margin-top: 42px;
}

.label{
  font-size: 18px;
}

.price{
  font-size: 18px;
}

.total-price-title{
  font-family: font-6;
  font-weight: 600;
  font-size: 24px;
  margin-right: 10px;
  color: #247c6c;
}

.total-price{
  font-family: font-6;
  font-size: 24px;
}

.currency{
  font-size: 24px;
}

.warranty-rules{
  margin-top: 20px;
}

.date-title,
.date{
  font-family: font-6;
  font-size: 24px;
}

.date-title{
  margin-right: 10px;
  color: #393939;
  font-weight: 600;
}

.date{
  font-weight: 400;
}

.merachnt-siganture-title,
.merchant-id-title,
.manager-siganture-title,
.manager-title{
  font-size: 18px;
  font-family: font-6;
  margin-right: 10px;
}

.merchant-id,
.manager{
  font-size: 18px;
  font-family: font-6;
}

</style>

</body>
</html>
