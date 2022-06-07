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
 <div class="container-fluid mt-4">
   <div class="row">

     <div class="col-md-8">

       <div class="row">
         <div class="col-md-12">
           <b class="label"> ს/კ </b>
           <span class="value"> {{ $code }} </span>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-md-12">
           <b class="label">დასახელება </b>
           <span class="value">{{ $name }}</span>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-md-12">
           <b class="label"> ელ. ფოსტა </b>
           <span class="value"> {{ $email }}</span>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-md-12">
           <b class="label"> ტელეფონი </b>
           <span class="value"> {{ $phone }}</span>
         </div>
       </div>

       <div class="row mt-2">
         <div class="col-md-12">
           <b class="label"> მისამართი </b>
           <span class="value"> {{ $address }}</span>
         </div>
       </div>

     </div>

     <div class="col-md-4">
       <img src="/images/general/logo.png" width="100%">
     </div>

   </div>
 </div>
</header>

<main class="mt-4">
  <div class="container-fluid">

   <div class="row">
    <div class="col-md-12">
      <div class="invoice-number-container">
       <h3 class="rules-title font-6 text-center"> ინვოისი #{{ $invoice }} </h3>
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
              <th scope="col" class="text-center">სულ ფასი</th>
            </tr>
          </thead>

          <tbody>

            @foreach($systemComponents as $index => $product)

            <tr>
              <th scope="row">{{ $index + 1 }}</th>
              <td>
                <span class="table-label">{{ $product['title'] }}</span>
              </td>

              <td class="text-center">
                <span class="table-label">{{ $product['quantity'] }}</span>
              </td>

              @if($index == 0)

              <td class="text-center align-middle" rowspan="{{ $numOfSystemComponents }}">
                <span class="table-label">{{ $systemPrice }}</span>
                <b class="price"> ₾ </b>
              </td>

              @endif

            </tr>

            @endforeach


            @foreach($products as $key => $product)
            <tr>
              <th scope="row">{{ $key + 1 }}</th>
              <td>
                <span class="table-label">{{ $product['title'] }}</span>
              </td>
              <td class="text-center">
                <span class="table-label">{{ $product['quantity'] }}</span>
              </td>
              <td class="text-center">
                <span class="table-label">{{ $product['price'] }}</span>
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
       <span class="date-title">თარიღი</span>
       <b class="date"> {{ date('d/m/Y') }} </b>
     </div>
    </div>

  </div>
</main>

<section class="mt-4">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-12">
        <b class="label">ინდ. მეწარმე </b>
        <span class="value">"ირაკლი კირვალიძე"</span>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-md-12">
        <b class="label"> საბანკო ანგარიში </b>
        <span class="value">GE30BG0000000162639879GEL</span>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-md-12">
        <b class="label"> ს/კ </b>
        <span class="value"> 20001065823 </span>
      </div>
    </div>

  </div>
</section>


<footer class="mt-5">
  <div class="container-fluid dashed-top-border">

    <div class="row">
      <div class="col-md-12">
        <b class="label">ტელეფონი: </b>
        <span class="value"> {{ $contact -> phone }} </span>
        <b class="value">ელ. ფოსტა: </b>
        <span class="value"> {{ $contact -> email }} </span>
      </div>

      <div class="col-md-12">
        <b class="label"> მისამართი: </b>
        <span class="value"> {{ $contact -> address }} </span>
      </div>

      <div class="col-md-12">
        <b class="label"> საიტის მისამართი: </b>
        <span class="value"> https:://www.itworks.ge  </span>
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

.table-label{
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

.label{
  font-size: 20px;
  font-family: font-6;
  margin-right: 10px;
}

.value{
  font-size: 20px;
  font-family: font-6;
}

.dashed-top-border{
  border-top: 1px dashed #a3a3a3;
  padding-top: 20px;
}

</style>

</body>
</html>
