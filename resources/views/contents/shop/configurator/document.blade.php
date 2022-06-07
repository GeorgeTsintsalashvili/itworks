<!doctype html>

<html>
<head>
 <meta charset="utf-8">
<head>
<body>

<div class="container">

<div class="head">
  <div class="logo">
    <img src="{address}/images/general/logo.png">
  </div>
  <div class="contact-information">
    <span class="phone">
      <b class="font-1">ტელეფონი: </b>
      <span class="font-2">{phone}</span>
    </span>
    <span class="email">
      <b class="font-1">ელ. ფოსტა: </b>
      <span class="font-2"> {email}</span>
    </span>
    <br>
    <span class="address">
      <b class="font-1">მისამართი: </b>
      <span class="font-2">{companyAddress}</span>
    </span>
    <br>
    <span class="schedule">
      <b class="font-1">განრიგი: </b>
      <span class="font-2">{schedule}</span>
    </span>
    <br>
    <span class="website">
      <b class="font-1">ჩვენი საიტის მისამართი: </b>
      <span class="font-2">https://www.itworks.ge</span>
    </span>
  </div>
</div>

 <div class="table">
  <div class="table-head">
   <span class="font-2 part-column"> მაკომპლექტებელი </span>
   <span class="font-2 price-column">ფასი</span>
  </div>
  <div class="table-body">
   <div class="table-row processor">
     <b class="font-1">პროცესორი: </b>
     <span class="font-2">{processorTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {processorDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{processorOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{processorPrice} </b>
      </span>
     </span>
   </div>
   <div class="table-row motherboard">
     <b class="font-1">დედაპლატა: </b>
     <span class="font-2">{motherboardTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {motherboardDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{motherboardOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{motherboardPrice}</b>
      </span>
     </span>
   </div>
   <div class="table-row memory">
     <b class="font-1">ოპერატიული: </b>
     <span class="font-2">{memoryTitle} ({memories} ცალი)</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {memoryDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{memoryOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{memoryPrice}</b>
      </span>
     </span>
   </div>
   <div class="table-row videoCard">
     <b class="font-1">ვიდეობარათი: </b>
     <span class="font-2">{videoCardTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {videoCardDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{videoCardOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{videoCardPrice} </b>
      </span>
     </span>
   </div>
   <div class="table-row powerSupply">
     <b class="font-1">კვების ბლოკი: </b>
     <span class="font-2">{powerSupplyTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {powerSupplyDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{powerSupplyOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{powerSupplyPrice}</b>
      </span>
     </span>
   </div>
   <div class="table-row hardDiskDrive">
     <b class="font-1">HDD: </b>
     <span class="font-2">{hardDiskDriveTitle} ({hardDiskDrives} ცალი)</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {hardDiskDriveDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{hardDiskDriveOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{hardDiskDrivePrice}</b>
      </span>
     </span>
   </div>
   <div class="table-row solidStateDrive">
     <b class="font-1">SSD: </b>
     <span class="font-2">{solidStateDriveTitle} ({solidStateDrives} ცალი)</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {solidStateDriveDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{solidStateDriveOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{solidStateDrivePrice} </b>
      </span>
     </span>
   </div>
   <div class="table-row processorCooler">
     <b class="font-1">პროცესორის ქულერი: </b>
     <span class="font-2">{processorCoolerTitle}</span>

     <span class="price-container">
       <span class="old-price font-1" style="display: {processorCoolerDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{processorCoolerOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{processorCoolerPrice} </b>
      </span>
     </span>

   </div>

   <div class="table-row case">
     <b class="font-1">კეისი: </b>
     <span class="font-2">{caseTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {caseDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{caseOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{casePrice} </b>
      </span>
     </span>
   </div>

   <div class="table-row monitor" style="display: {monitorDisplay}">
     <b class="font-1">მონიტორი: </b>
     <span class="font-2">{monitorTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {monitorDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{monitorOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{monitorPrice} </b>
      </span>
     </span>
   </div>

   <div class="table-row headphone" style="display: {headphoneDisplay}">
     <b class="font-1">ყურსასმენი: </b>
     <span class="font-2">{headphoneTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {headphoneDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{headphoneOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{headphonePrice} </b>
      </span>
     </span>
   </div>

   <div class="table-row keyboard" style="display: {keyboardDisplay}">
     <b class="font-1">კლავიატურა: </b>
     <span class="font-2">{keyboardTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {keyboardDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{keyboardOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{keyboardPrice} </b>
      </span>
     </span>
   </div>

   <div class="table-row computerMouse" style="display: {computerMouseDisplay}">
     <b class="font-1">მაუსი: </b>
     <span class="font-2">{computerMouseTitle}</span>
     <span class="price-container">
       <span class="old-price font-1" style="display: {computerMouseDiscountVisibility}">
        <img src="{address}/images/general/lari-discount.png">
        <b>{computerMouseOldPrice} </b>
       </span>
      <span class="current-price font-1">
       <img src="{address}/images/general/lari-primary-sign.png">
       <b>{computerMousePrice} </b>
      </span>
     </span>
   </div>

  </div>
 </div>

<div class="footer">
  <div class="assembly-price">
    <span class="font-1">აწყობის ღირებულება: </span>
    <img src="{address}/images/general/lari-primary-sign.png">
    <b class="font-1">{assemblyPrice}</b>
  </div>

  <div class="total-price">
    <span class="total-current-price">
      <span class="font-1">კონფიგურაციის ფასი: </span>
      <img src="{address}/images/general/lari-primary-sign.png">
      <b class="font-1">{configurationPrice}</b>
    </span>
    <span class="total-old-price" style="display: {overalOldPriceVisibility}">
      <img src="{address}/images/general/lari-discount.png">
      <b class="font-1">{oldPrice}</b>
    </span>
  </div>

  <div class="config-gen">
   <span class="font-1">კონფიგურაცია დაგენერირდა: </span>
   <b class="font-1"> {time} </b>
  </div>

  <div class="assembly-time">
   <h1 class="font-1">სისტემური ბლოკი იწყობა შეკვეთიდან 24 საათში!</h1>
  </div>

</div>

</div>

<style type="text/css">

html,
body{
  margin: 0;
  padding: 0;
}

.head{
  position: relative;
  width: 100%;
}

.font-1{
  font-family: DejaVu Sans
}

.font-2{
  font-family: DejaVu Sans;
  font-weight: 400;
}

.container{
  width: 1070px;
  margin: 0 auto;
  position: relative;
}

.contact-information{
  position: absolute;
  width: 80%;
  left: 20%;
  top: 0;
  text-align: right;
}

.logo{
  margin-bottom: 10px;
  margin-top: 15px;
  width: 20%;
}

.logo img{
  width: 100%;
  display: block;
  margin-bottom: 20px;
}

.table .table-head{
  background-color: #ebebeb;
  height: 40px;
  line-height: 25px;
  padding-left: 10px;
  border: 1px solid #bfbfbf;
  margin-bottom: 10px;
  font-size: 18px;
}

.table-head .price-column{
  position: absolute;
  right: 20px;
}

.table .table-row{
  position: relative;
  padding-bottom: 6px;
  padding-top: 4px;
}

.table .table-row{
  border-bottom: 1px solid #bfbfbf;
  border-right: 1px solid #bfbfbf;
  border-left: 1px solid #bfbfbf;
  padding-left: 10px;
}

.table .table-row b + span{
   font-size: 14px;
}

.table .table-row:first-child{
  margin-top: -10px
}

.price-container{
  position: absolute;
  right: 10px;
  top: 2px;
}

.current-price img,
.old-price img{
  height: 16px;
}

.current-price b{
  font-size: 22px;
  color: #454545;
  font-weight: 200;
}

.old-price b{
  font-size: 22px;
  color: #888888;
  font-weight: 200;
  text-decoration: line-through;
}

.footer{
  margin-top: 5px;
  position: relative;
}

.assembly-price img,
.total-current-price img,
.total-old-price img{
  width: 18px;
}

.total-old-price img{
  margin-left: 10px;
}

.assembly-price span{
  color: #0a756a;
  font-size: 20px;
  font-weight: bold;
}

.config-gen span {
  color: #4c4848;
  font-size: 20px;
  font-weight: bold;
}

.config-gen b {
  color: #0a756a;
  font-size: 20px;
  font-weight: bold;
}

.total-current-price span{
  color: #4c4848;
  font-size: 20px;
  font-weight: bold;
}

.assembly-price{
  position: absolute;
  top: 0;
  right: 10px;
}

.assembly-price b{
  font-size: 22px;
  font-weight: 200;
}

.total-current-price b,
.total-old-price b,
.config-gen b{
  font-size: 22px;
}

.total-current-price{
  color: #454545;
}

.total-old-price b{
  color: #888888;
  text-decoration: line-through;
}

.total-current-price span{
  margin-right: 8px;
}

.footer .assembly-time{
  margin-top: 0;
}

.footer .assembly-time h1{
  font-size: 20px;
  text-align: center;
}

</style>

</body>
</html>
