<!DOCTYPE html>

<html lang="ka">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  @if($generalData['seoFieldsExist'])

  <title>{{ $generalData['seoFields'] -> title }}</title>

  <meta property="og:type" content="website" />
  <meta property="og:url" content="{{ url() -> current() }}" />
  <meta property="og:title" content="{{ $generalData['seoFields'] -> title }}" />
  <meta property="og:description" content="{{ $generalData['seoFields'] -> description }}" />
  <meta property="og:image" content="{{ url('/') }}@yield('facebookShareImage', '/images/general/logo.png')" />

  @endif

  <meta name="scrt" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <link rel="icon" href="/images/general/logo.ico" />

  <!--- fonts styles --->

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:200,200i,300,300i,400,400i,500,500i,600,600i,700,700i" media="all" />
  <link rel="stylesheet" href="/fonts/various/css/fonts.css?v=34" type="text/css" media="all" />
  <link rel="stylesheet" href="/fonts/font-awesome-pro/css/pro.min.css?v=34" type="text/css" media="all" />
  <link rel="stylesheet" href="/fonts/flaticon/computer-parts/font/flaticon.css?v=34" type="text/css" media="all" />

  <!--- vendor styles --->

  <link rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css?v=34" type="text/css" media="all" />
  <link rel="stylesheet" href="/vendor/splide/css/splide.min.css?v=34" type="text/css" media="all" />

  <!--- general styles --->

  <link rel="stylesheet" href="/css/modules.css?v=43" type="text/css" media="all" />
  <link rel="stylesheet" href="/css/general.css?v=43" type="text/css" media="all" />

  <!--- vendor scripts --->

  <script type="text/javascript" src="/vendor/axios/axios.min.js?v=37"></script>
  <script type="text/javascript" src="/vendor/splide/js/splide.min.js?v=37"></script>
  <script type="text/javascript" src="/vendor/bootstrap/js/bootstrap.min.js?v=37"></script>

  <!--- general scripts --->

  <script type="text/javascript" src="/js/general.js?v=37"></script>
  <script type="text/javascript" src="/js/search.js?v=37"></script>
  <script type="text/javascript" src="/js/validation.js"></script>

 </head>

 <body>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script type="text/javascript" async src="https://www.googletagmanager.com/gtag/js?id=UA-203663349-1"></script>

  <script type="text/javascript">
   window.dataLayer = window.dataLayer || [];
   function gtag() {
    dataLayer.push(arguments);
   }
   gtag("js", new Date());
   gtag("config", "UA-203663349-1");
  </script>

  <!--- Load Facebook SDK --->
  <div id="fb-root"></div>

  <script type="text/javascript">
   (function (d, s, id) {
    var js,
     fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11";
    fjs.parentNode.insertBefore(js, fjs);
   })(document, "script", "facebook-jssdk");
  </script>

  <!-- Load Facebook Messenger SDK for JavaScript -->
  <script type="text/javascript">
   (function (d, s, id) {
    var js,
     fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js";
    fjs.parentNode.insertBefore(js, fjs);
   })(document, "script", "facebook-jssdk");
  </script>

  <!-- Your Chat Plugin code -->
  <div class="fb-customerchat" attribution="setup_tool" page_id="425829564213269" logged_in_greeting="გამარჯობა, რით შემიძლია დაგეხმაროთ?" logged_out_greeting="გამარჯობა, რით შემიძლია დაგეხმაროთ?"></div>

  <div id="page" class="headerskin1 full">
   <!-- topbar section -->
   <div id="itw-topbar">
    <div class="container">
     <div class="row">
      <!--- schedule -->
      <div class="col-md-3">
       <span> <i class="far fa-calendar-alt"></i> {{ $generalData['contact'] -> schedule }} </span>
      </div>
      <!--- phone number -->
      <div class="col-md-2">
       <span> <i class="fa fa-phone"></i> {{ $generalData['contact'] -> phone }} </span>
      </div>
      <!--- email address -->
      <div class="col-md-2">
       <span> <i class="fa fa-envelope"></i> {{ $generalData['contact'] -> email }} </span>
      </div>
      <!--- address -->
      <div class="col-md-3">
       <a href="{{ $generalData['contact'] -> googleMapLink }}" target="_blank">
        <span class="font-4"> <i class="fas fa-map-marker" aria-hidden="true"></i> {{ $generalData['contact'] -> address }} </span>
       </a>
      </div>
      <!--- delivery -->
      <div class="col-md-2">
       <span class="font-4"> <i class="fas fa-truck"></i> {{ $generalData['contact'] -> delivery }} </span>
      </div>
     </div>
    </div>
   </div>

   <!-- header section -->

   <header id="header">
    <!--- header top part start --->

    <div class="header-top-part mt-4">
     <div class="container">
      <div class="row align-items-center">
       <!--- main navigation column --->

       <div class="col-md-12">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
         <a id="header-logo" class="navbar-brand" href="/">
          <img class="img-responsive" src="/images/general/logo.png?v=2" height="100px" />
         </a>

         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav mr-auto ml-auto flex-wrap">
           <li class="nav-item">
            <a class="nav-link" target="_self" href="/">
             <i class="fa fa-home menu-icon" aria-hidden="true"></i>
             <span class="menu-title font-6">მთავარი</span>
            </a>
           </li>
           <li class="parent dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-store menu-icon" aria-hidden="true"></i>
             <span class="menu-title font-6">მაღაზია</span>
            </a>

            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
             <div class="d-md-flex align-items-start justify-content-start">
              <div>
               <!--- computer components --->
               <div class="dropdown-header">
                <span>კომპიუტერის ნაწილები</span>
               </div>
               <a class="dropdown-item" href="{{ route('cpu') }}">პროცესორები</a>
               <a class="dropdown-item" href="{{ route('mb') }}">დედაპლატები</a>
               <a class="dropdown-item" href="{{ route('mm') }}">ოპერატიულები</a>
               <a class="dropdown-item" href="{{ route('vc') }}">ვიდეო ბარათები</a>
               <a class="dropdown-item" href="{{ route('hdd') }}">HDD მეხსიერება</a>
               <a class="dropdown-item" href="{{ route('ssd') }}">SSD მეხსიერება</a>
               <a class="dropdown-item" href="{{ route('ps') }}">კვების ბლოკები</a>
               <a class="dropdown-item" href="{{ route('cases') }}">კეისები</a>
               <a class="dropdown-item" href="{{ route('pc') }}">პროცესორის ქულერები</a>
               <a class="dropdown-item" href="{{ route('cc') }}">კეისის ქულერები</a>
               <a class="dropdown-item" href="{{ route('odd') }}">ოპტიკური დისკმძრავები</a>
               <!--- peripherals --->
               <div class="dropdown-header mt-2">
                <span>პერიფერიალები</span>
               </div>
               @foreach($generalData['peripheralsTypes'] as $value)
               <a class="dropdown-item" href="{{ route('perByType', ['categoryId' => $value -> id]) }}">{{ $value -> typeTitle }}</a>
               @endforeach
               <a class="dropdown-item" href="{{ route('monitors') }}">მონიტორები</a>
               <a class="dropdown-item" href="{{ route('ups') }}">უწყვეტი კვების წყარო</a>
              </div>
              <div>
               <!--- accessories --->
               <div class="dropdown-header">
                <span>აქსესუარები</span>
               </div>
               @foreach($generalData['accessoriesTypes'] as $value)
               <a class="dropdown-item" href="{{ route('accByType', ['categoryId' => $value -> id]) }}">{{ $value -> typeTitle }}</a>
               @endforeach
               <a class="dropdown-item" href="{{ route('nc') }}">ნოუთბუქის დამტენები</a>
               <!--- computers --->
               <div class="dropdown-header mt-2">
                <span>კომპიუტერები</span>
               </div>
               <a class="dropdown-item" href="{{ route('sb') }}">სისტემური ბლოკები</a>
               <a class="dropdown-item" href="{{ route('laptops') }}">ლეპტოპები</a>
              </div>
              <div>
               <!--- network hardware --->
               <div class="dropdown-header">
                <span>ქსელური აპარატურა</span>
               </div>
               @foreach($generalData['networkDevicesTypes'] as $key => $value)
               <a class="dropdown-item" href="{{ route('ndByType', ['categoryId' => $value -> id]) }}">{{ $value -> typeTitle }}</a>
               @endforeach
              </div>
             </div>
            </div>
           </li>
           <li class="nav-item">
            <a class="nav-link" href="#">
             <i class="fas fa-marker"></i>
             <span class="menu-title font-6">საგარანტიო პირობები</span>
            </a>
           </li>
           <li class="nav-item">
            <a class="nav-link" href="/configurator">
             <i class="fas fa-cog menu-icon"></i>
             <span class="menu-title font-6">კონფიგურატორი</span>
            </a>
           </li>
           <li class="nav-item">
            <a class="nav-link" href="/contact">
             <i class="fa fa-address-book menu-icon"></i>
             <span class="menu-title font-6">კონტაქტი</span>
            </a>
           </li>

           @if(!Auth::guard('shopUser')->check())
           <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#authorization-modal">
             <i class="fas fa-unlock-alt"></i>
             <span class="menu-title font-6">ავტორიზაცია</span>
            </a>
           </li>
           @else
           <li class="nav-item parent dropdown profile-dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <i class="fas fa-user"></i>
             <span class="menu-title font-6">მომხმარებელი</span>
            </a>

            <div class="dropdown-menu" aria-labelledby="profileDropdown">
             <div class="d-md-flex align-items-start justify-content-start">
              <div class="profile-dropdown-items">
               <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#user-info-modal">პირადი ინფორმაცია</a>
               <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#change-password-modal">პაროლის შეცვლა</a>
               <a class="dropdown-item" href="{{ route('shopUserMessages') }}">შეტყობინებები</a>
               <a class="dropdown-item" href="{{ route('shopUserOrders') }}">შეკვეთები</a>
               <a class="dropdown-item" href="{{ route('shoppingCart') }}">კალათა</a>
              </div>
             </div>
            </div>

           </li>
           <li class="nav-item">
            <a class="nav-link" href="{{ route('shop.logout') }}">
             <i class="fas fa-sign-out-alt"></i>
             <span class="menu-title font-6">გამოსვლა</span>
            </a>
           </li>
           <li class="nav-item shopping-cart-nav">
            <a class="nav-link" href="{{ route('shoppingCart') }}">
             <i class="fas fa-shopping-basket menu-icon"></i>
             <span class="menu-title font-6">{{ Auth::guard('shopUser') -> user() -> cartItemsQuantity() }}</span>
            </a>
           </li>
           @endif
          </ul>
         </div>
        </nav>
       </div>
      </div>
     </div>
    </div>

    <!--- header top part end --->

    <div class="header-bottom-part">
     <div class="container">
      <div class="row">
       <!--- vertical menu --->
       <div id="vertical-menu-container" class="col-md-4 block block-info nopadding closes">
        <div class="vertical-menu-title">
         <div class="vertical-menu-head">
          <div class="vertical-menu-head-inner">
           <div class="vertical-menu-head-wrapper">
            <div class="fa-icon-menu">
             <i class="fa fa-list"></i>
            </div>
            <div class="vertical-menu-title-block">
             <span class="font-5 product-categories">პროდუქციის კატალოგი</span>
            </div>
           </div>
          </div>
         </div>
        </div>
        <div class="vertical-menu-wrapper">
         <div class="itw-vertical-menu block_content">
          <div class="navbar navbar-default">
           <div class="controll-vertical-mobile clearfix">
            <div class="close-vertical-menu" style="display: none">
             <span>
              <i class="fas fa-times"></i>
             </span>
            </div>
           </div>
           <div class="verticalmenu">
            <div class="navbar-header">
             <div class="navbar-collapse navbar-ex1-collapse">
              <ul class="nav navbar-nav verticalmenu flex-row">
               <li>
                <a target="_self" href="{{ route('sb') }}">
                 <i class="flaticon-016-tower"></i>
                 <span class="menu-title font-5"> სისტემური ბლოკები</span>
                </a>
               </li>

               <li>
                <a target="_self" href="{{ route('laptops') }}">
                 <i class="fas fa-laptop"></i>
                 <span class="menu-title font-5"> ლეპტოპები </span>
                </a>
               </li>

               <li>
                <a target="_self" href="{{ route('monitors') }}">
                 <i class="fas fa-desktop"></i>
                 <span class="menu-title font-5"> მონიტორები</span>
                </a>
               </li>

               <li class="has-menu is-closed">
                <a>
                 <i class="fas fa-tools"></i>
                 <span class="menu-title font-5"> კომპიუტერის ნაწილები </span>
                </a>
                <ul class="sub-menu">
                 <li>
                  <div class="row">
                   <div class="col-sm-12">
                    <div class="widget-content">
                     <div class="widget-links">
                      <div class="widget-inner block_content">
                       <div class="panel-group">
                        <ul class="nav-links">
                         <li>
                          <a href="{{ route('cpu') }}">
                           <i class="fas fa-microchip"></i>
                           <span class="font-5"> პროცესორები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('mb') }}">
                           <i class="flaticon-033-motherboard"></i>
                           <span class="font-5"> დედაპლატები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('mm') }}">
                           <i class="fas fa-memory"></i>
                           <span class="font-5"> ოპერატიულები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('vc') }}">
                           <i class="flaticon-026-sound-card"></i>
                           <span class="font-5"> ვიდეო ბარათები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('hdd') }}">
                           <i class="flaticon-005-hdd"></i>
                           <span class="font-5"> HDD მეხსიერება</span>
                          </a>
                         </li>

                         <li>
                          <a href="{{ route('ssd') }}">
                           <i class="flaticon-004-ssd"></i>
                           <span class="font-5"> SSD მეხსიერება</span>
                          </a>
                         </li>

                         <li>
                          <a href="{{ route('cases') }}">
                           <i class="flaticon-003-tower-1"></i>
                           <span class="font-5"> კეისები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('ps') }}">
                           <i class="flaticon-002-supply"></i>
                           <span class="font-5"> კვების ბლოკები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('pc') }}">
                           <i class="flaticon-019-cooler"></i>
                           <span class="font-5"> პროცესორის ქულერები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('cc') }}">
                           <i class="flaticon-015-cooler-1"></i>
                           <span class="font-5"> კეისის ქულერები</span>
                          </a>
                         </li>
                         <li>
                          <a href="{{ route('odd') }}">
                           <i class="flaticon-001-cd-drive"></i>
                           <span class="font-5"> დისკმძრავები</span>
                          </a>
                         </li>
                        </ul>
                       </div>
                      </div>
                     </div>
                    </div>
                   </div>
                  </div>
                 </li>
                </ul>
               </li>

               <li class="has-menu is-closed">
                <a>
                 <i class="flaticon-018-mouse"></i>
                 <span class="menu-title font-5"> აქსესუარები </span>
                </a>
                <ul class="sub-menu">
                 <li>
                  <div class="row">
                   <div class="col-sm-12">
                    <div class="widget-content">
                     <div class="widget-links">
                      <div class="widget-inner block_content">
                       <div class="panel-group">
                        <ul class="nav-links">
                         @foreach($generalData['accessoriesTypes'] as $value)
                         <li>
                          <a href="{{ route('accByType', ['categoryId' => $value -> id]) }}">
                           <i class="{{ $value -> icon }}"></i>
                           <span class="font-5"> {{ $value -> typeTitle }} </span>
                          </a>
                         </li>
                         @endforeach
                        </ul>
                       </div>
                      </div>
                     </div>
                    </div>
                   </div>
                  </div>
                 </li>
                </ul>
               </li>

               <li class="has-menu is-closed" >
                <a>
                 <i class="fas fa-network-wired"></i>
                 <span class="menu-title font-5"> ქსელური მოწყობილობები </span>
                </a>
                <ul class="sub-menu">
                 <li>
                  <div class="row">
                   <div class="col-sm-12">
                    <div class="widget-content">
                     <div class="widget-links">
                      <div class="widget-inner block_content">
                       <div class="panel-group">
                        <ul class="nav-links">
                         @foreach($generalData['networkDevicesTypes'] as $value)
                         <li>
                          <a href="{{ route('ndByType', ['categoryId' => $value -> id]) }}">
                           <i class="{{ $value -> icon }}"></i>
                           <span class="font-5"> {{ $value -> typeTitle }} </span>
                          </a>
                         </li>
                         @endforeach
                        </ul>
                       </div>
                      </div>
                     </div>
                    </div>
                   </div>
                  </div>
                 </li>
                </ul>
               </li>

               <li class="has-menu is-closed">
                <a>
                 <i class="flaticon-012-printer-1"></i>
                 <span class="menu-title font-5"> პერიფერიალები</span>
                </a>
                <ul class="sub-menu">
                 <li>
                  <div class="row">
                   <div class="col-sm-12">
                    <div class="widget-content">
                     <div class="widget-links">
                      <div class="widget-inner block_content">
                       <div class="panel-group">
                        <ul class="nav-links">
                         @foreach($generalData['peripheralsTypes'] as $key => $value)
                         <li>
                          <a href="{{ route('perByType', ['categoryId' => $value -> id]) }}">
                           <i class="{{ $value -> icon }}"></i>
                           <span class="font-5"> {{ $value -> typeTitle }} </span>
                          </a>
                         </li>
                         @endforeach
                        </ul>
                       </div>
                      </div>
                     </div>
                    </div>
                   </div>
                  </div>
                 </li>
                </ul>
               </li>
               <li>
                <a target="_self" href="{{ route('ups') }}">
                 <i class="fa fa-plug"></i>
                 <span class="menu-title font-5"> უწყვეტის კვების წყარო (UPS)</span>
                </a>
               </li>
               <li>
                <a target="_self" href="{{ route('nc') }}">
                 <i class="fas fa-charging-station"></i>
                 <span class="menu-title font-5"> ნოუთბუქის დამტენები</span>
                </a>
               </li>
              </ul>
             </div>
            </div>
           </div>
          </div>
         </div>
        </div>
       </div>

       <!-- products search module -->

       <div id="itw-top-search" class="col-md-8">
        <form method="get" action="/search" id="searchbox" class="form-inline">
         <div class="itw-search form-group">
          <div id="live-search-container" style="display: none">
           <ul class="search-result-list container"></ul>
          </div>

          <input type="text" name="query" id="top-search-query-input" value="" placeholder="ჩაწერეთ ტექსტი..." class="search_query form-control font-6" autocomplete="off" />
          <input type="hidden" name="categoryId" id="category" value="f1u3ja5i7" />
          <div class="pos-search form-group no-uniform">
           <div class="choose-category-lists">
            <div class="choose-category-lists-inner">
             <div class="choose-category-lists-wrapper">
              <div class="choose-category-lists-content">
               <span class="font-5" data-bind="label">ყველა კატეგორია</span>
               <span class="fa fa-angle-down"></span>
              </div>
             </div>
            </div>
           </div>
           <ul class="dropdown-menu search-category-lists scroll-div">
            <li>
             <a href="#" data-category-id="f1u3ja5i7">
              <i class="fa fa-angle-double-right" aria-hidden="true"></i>
              <span class="font-5"> ყველა კატეგორია</span>
             </a>
            </li>

            @foreach($generalData['tables'] as $table)
            <li>
             <a href="#" data-category-id="{{ $table -> alias }}">
              <i class="fa fa-angle-double-right" aria-hidden="true"></i>
              <span class="font-5"> {{ $table -> title }} </span>
             </a>
            </li>
            @endforeach
           </ul>
          </div>
          <button type="submit" class="btn btn-secondary submit-search">
           <i class="fa fa-search"></i>
          </button>
         </div>
        </form>
       </div>
      </div>
     </div>
    </div>
   </header>

   <!-- page content start-->

   @yield('content')

   <!-- page content end-->

   <!-- footer -->
   <footer id="footer">
    <div class="container">
     <div class="row">
      <div class="col-md-12">
       <div class="d-flex align-items-center justify-content-between">
        <!--- copyright --->
        <div id="copyright">
         <b>{{ $generalData['contact'] -> companyName }}</b> © {{ date('Y') }}
         <span> ყველა უფლება დაცულია</span>
        </div>

        <!--- site counter --->
        <div id="top-ge-counter-container" data-site-id="113303">
         <a href="http://www.top.ge/index.php?h=113303#113303" target="_blank">
          <img src="/images/general/rating.jpg" alt="TOP.GE" />
         </a>
        </div>
        <!---  <script async src="https://counter.top.ge/counter.js"></script> --->
       </div>
      </div>
     </div>
    </div>
   </footer>
  </div>

  <div class="modal" tabindex="-1" id="authorization-modal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active font-5" id="user-authorization-tab" data-bs-toggle="tab" href="#user-authorization" role="tab" aria-controls="user-authorization">ავტორიზაცია</a>
            </li>
            <li class="nav-item">
              <a class="nav-link font-5" id="user-registration-tab" data-bs-toggle="tab" href="#user-registration" role="tab" aria-controls="user-registration" aria-selected="false">რეგისტრაცია</a>
            </li>
          </ul>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="დახურვა"></button>
        </div>

        <div class="tab-content">
          <div class="tab-pane fade active show" id="user-authorization" role="tabpanel" aria-labelledby="user-authorization-tab">
           <form action="{{ route('shop.login') }}" id="authorization-form">
             <div class="modal-body">
               <div class="mb-3">
                 <label for="auth-email" class="col-form-label font-7">ელ. ფოსტა</label>
                 <input type="text" class="form-control shadow-none" name="email" maxlength="100" id="auth-email">
               </div>
               <div class="mb-4">
                 <label for="auth-password" class="col-form-label font-7">პაროლი</label>
                 <input type="password" class="form-control shadow-none" name="password" maxlength="100" id="auth-password">
               </div>
               <div class="mb-3">
                <div class="modal-checkbox remember">
                 <span class="modal-checkbox-square unchecked"></span>
                 <span class="modal-checkbox-text font-6 noselect">დამახსოვრება</span>
                 <input type="hidden" value="0" name="remember">
                </div>
               </div>

               <div class="mb-4 mt-4">
                <div class="form-validation-errors" id="login-errors-block" style="display: none"></div>
               </div>

               <button type="submit" class="btn btn-primary shadow-none font-5 mt-3">ავტორიზაცია</button>
             </div>
             <div class="modal-footer justify-content-start">
               <button type="button" class="nav-link btn btn-secondary shadow-none font-5" data-bs-toggle="modal" data-bs-target="#password-reset-modal" data-bs-dismiss="modal">პაროლის აღდგენა</button>
             </div>
           </form>
          </div>

          <div class="tab-pane fade" id="user-registration" role="tabpanel" aria-labelledby="user-registration-tab">
           <form action="{{ route('shop.register') }}" id="registration-form">
             <div class="modal-body">
               <div class="mb-3">
                 <label for="reg-name" class="col-form-label font-7">სახელი და გვარი</label>
                 <input type="text" class="form-control shadow-none" name="name" maxlength="100" id="reg-name">
               </div>
               <div class="mb-3">
                 <label for="reg-email" class="col-form-label font-7">ელ. ფოსტა</label>
                 <input type="text" class="form-control shadow-none" name="email" maxlength="100" id="reg-email">
               </div>
               <div class="mb-3">
                 <label for="reg-phone" class="col-form-label font-7">მობ. ტელეფონი</label>
                 <input type="text" class="form-control shadow-none" name="phone" maxlength="50" id="reg-phone">
               </div>
               <div class="mb-3">
                 <label for="reg-password" class="col-form-label font-7">პაროლი (მინიმუმ 8 სიმბოლო)</label>
                 <div class="input-wrapper">
                   <input type="password" class="form-control shadow-none" name="password" maxlength="100" id="reg-password">
                   <span class="password-visibility">
                    <i class="fas fa-eye-slash"></i>
                   </span>
                 </div>
               </div>
               <div class="mb-3">
                 <label for="reg-password-confirmation" class="col-form-label font-7">გაიმეორეთ პაროლი</label>
                 <div class="input-wrapper">
                   <input type="password" class="form-control shadow-none" name="password_confirmation" maxlength="100" id="reg-password-confirmation">
                   <span class="password-visibility">
                     <i class="fas fa-eye-slash"></i>
                   </span>
                 </div>
               </div>
               <div class="mb-4">
                <div class="form-validation-errors" id="reg-errors-block" style="display: none"></div>
               </div>
             </div>
             <div class="modal-footer justify-content-start">
               <button type="submit" class="btn btn-primary shadow-none font-5">რეგისტრაცია</button>
             </div>
           </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" id="password-reset-modal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-5"> პაროლის აღდგენა </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="დახურვა"></button>
        </div>
        <form action="{{ route('shop.password.email') }}" id="password-reset-form">
          <div class="modal-body">
            <div class="mb-3">
              <label for="reset-email" class="col-form-label font-7">ელ. ფოსტა</label>
              <input type="text" class="form-control shadow-none" name="email" maxlength="100" id="reset-email">
            </div>
            <div class="mb-4">
             <div class="form-validation-errors" id="password-reset-errors-block" style="display: none"></div>
            </div>
            <button type="submit" class="btn btn-primary shadow-none font-5 mt-3">აღდგენის ბმულის გაგზავნა</button>
          </div>
          <div class="modal-footer justify-content-start">
            <button type="button" class="btn btn-secondary shadow-none font-5" data-bs-toggle="modal" data-bs-target="#authorization-modal" data-bs-dismiss="modal">ავტორიზაცია</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" id="change-password-modal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-5"> პაროლის შეცვლა </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="დახურვა"></button>
        </div>
        <form action="{{ route('changeShopUserPassword') }}" id="password-change-form">
          <div class="modal-body">
            <div class="mb-3">
              <label for="old-password" class="col-form-label font-7">ძველი პაროლი</label>
              <input type="password" class="form-control shadow-none" name="old-password" maxlength="50" id="old-password">
            </div>
            <div class="mb-3">
              <label for="password-confirmation" class="col-form-label font-7">ახალი პაროლი (მინიმუმ 8 სიმბოლო)</label>
              <div class="input-wrapper">
                <input type="password" class="form-control shadow-none" name="new-password" maxlength="50" id="password-confirmation">
                <span class="password-visibility">
                 <i class="fas fa-eye-slash"></i>
                </span>
              </div>
            </div>
            <div class="mb-3">
              <label for="new-password-confirmation" class="col-form-label font-7">გაიმეორეთ პაროლი</label>
              <div class="input-wrapper">
                <input type="password" class="form-control shadow-none" name="new-password-confirmation" maxlength="50" id="new-password-confirmation">
                <span class="password-visibility">
                  <i class="fas fa-eye-slash"></i>
                </span>
              </div>
            </div>

            <div class="mb-4">
             <div class="form-validation-errors" id="password-change-errors-block" style="display: none"></div>
            </div>

            <div class="button-row mt-4 mb-3">
             <button type="submit" class="btn btn-primary shadow-none font-5">გაგზავნა</button>
             <button type="button" class="btn btn-secondary shadow-none font-5 ms-4" id="clear-password-change-form">გასუფთავება</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if(Auth::guard('shopUser')->check())

  <div class="modal" tabindex="-1" id="user-info-modal">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-5"> პირადი ინფორმაცია </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="დახურვა"></button>
        </div>
        <form action="{{ route('changeShopUserInfo') }}" id="user-info-form">
          <div class="modal-body">
            <div class="mb-3">
              <label for="info-email" class="col-form-label font-7">ელექტრონული ფოსტა</label>
              <input type="text" class="form-control shadow-none font-7" disabled name="email" value="{{ Auth::guard('shopUser') -> user() -> email }}" maxlength="200" id="info-email">
            </div>
            <div class="mb-3">
              <label for="info-name" class="col-form-label font-7">სახელი და გვარი</label>
              <input type="text" class="form-control shadow-none font-7" name="name" value="{{ Auth::guard('shopUser') -> user() -> name }}" maxlength="200" id="info-name">
            </div>
            <div class="mb-3">
              <label for="info-phone" class="col-form-label font-7">მობ. ტელეფონი</label>
              <input type="text" class="form-control shadow-none" name="phone" value="{{ Auth::guard('shopUser') -> user() -> phone }}" data-regex="^\+?\d{4,50}$" maxlength="50" id="info-phone">
            </div>
            <div class="mb-3">
              <label for="info-address" class="col-form-label font-7">ფიზიკური მისამართი (არაა სავალდებულო)</label>
              <input type="text" class="form-control shadow-none font-7 optional-field" name="address" value="{{ Auth::guard('shopUser') -> user() -> address }}" maxlength="500" id="info-address">
            </div>

            <div class="mb-4">
             <div class="form-validation-errors" id="info-update-errors-block" style="display: none"></div>
            </div>

            <button type="submit" class="btn btn-primary shadow-none font-5 mt-3 mb-3">მონაცემების შენახვა</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @endif

  <script type="text/javascript" src="/js/modals.js"></script>

  @if($generalData['informationToUsers'] -> visibility && !$generalData['displayMessageCookieIsDefined'])

  <!--Notification start-->
  <section id="message-to-users">
   <div class="message-container">
    <div class="wrapper">
     <div class="message-area">
      <h1 class="text-to-output font-4">{{ $generalData['informationToUsers'] -> text }}</h1>
      <span class="close-block">
       <i class="fa fa-times"></i>
      </span>
      <script type="text/javascript">
       document.getElementById("message-to-users").addEventListener("click", function(){
         this.style = "display: none";
       });
      </script>
     </div>
    </div>
   </div>
  </section>
  <!--Notification end-->

  @endif
 </body>
</html>
