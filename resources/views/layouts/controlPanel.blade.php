
<!DOCTYPE html>

<html lang="ka">

<head>

    <title>მართვის პანელი</title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="description" content="">

    <link type="image/ico" rel="icon" href="/images/general/logo.ico"/>
    <link type="text/css" rel="stylesheet" href="/admin/css/snackbar.css?v=35"/>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=RobotoDraft:300,400,400italic,500,700"/>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400italic,600,700"/>

    <link type="text/css" rel="stylesheet" href="/admin/fonts/various/css/fonts.css?v=35" media="all" />
    <link type="text/css" rel="stylesheet" href="/admin/fonts/flaticon/computer-parts/font/flaticon.css?v=35" media="all" />
    <link type="text/css" rel="stylesheet" href="/admin/fonts/font-awesome-pro/css/pro.min.css?v=35"> <!-- Font Awesome -->
    <link type="text/css" href="/admin/css/styles.css?v=44" rel="stylesheet"> <!-- Core CSS with all styles -->

    <link type="text/css" href="/admin/plugins/jstree/dist/themes/avenger/style.min.css?v=35" rel="stylesheet"> <!-- jsTree -->
    <link type="text/css" href="/admin/plugins/codeprettifier/prettify.css?v=35" rel="stylesheet"> <!-- Code Prettifier -->
    <link type="text/css" href="/admin/plugins/iCheck/skins/minimal/blue.css?v=35" rel="stylesheet"> <!-- iCheck -->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries. Placeholdr.js enables the placeholder attribute -->

    <link type="text/css" href="/admin/plugins/form-select2/select2.css?v=35" rel="stylesheet"> <!-- Select2 -->
    <link type="text/css" href="/admin/plugins/form-multiselect/css/multi-select.css?v=35" rel="stylesheet"> <!-- Multiselect -->
    <link type="text/css" href="/admin/plugins/form-tokenfield/bootstrap-tokenfield.css?v=35" rel="stylesheet"> <!-- Tokenfield -->
    <link type="text/css" href="/admin/plugins/switchery/switchery.css?v=35" rel="stylesheet">  <!-- Switchery -->

    <link type="text/css" href="/admin/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css?v=35" rel="stylesheet"> <!-- Touchspin -->
    <link type="text/css" href="/admin/js/jqueryui.css?v=35" rel="stylesheet"> <!-- jQuery UI CSS -->

    <link type="text/css" href="/admin/plugins/iCheck/skins/minimal/_all.css?v=35" rel="stylesheet"> <!-- Custom Checkboxes / iCheck -->
    <link type="text/css" href="/admin/plugins/iCheck/skins/flat/_all.css?v=35" rel="stylesheet">
    <link type="text/css" href="/admin/plugins/iCheck/skins/square/_all.css?v=35" rel="stylesheet">
    <link type="text/css" href="/admin/plugins/card/lib/css/card.css?v=35" rel="stylesheet"> <!-- Card -->

    <!-- Add Input style JS and CSS files -->
    <link rel="stylesheet" type="text/css" href="/admin/plugins/custom-file-input/css/component.css?v=35">
    <script type="text/javascript" src="/admin/js/jquery-1.10.2.min.js?v=35"></script>

    <!--- fancybox --->
    <script type="text/javascript" src="/admin/plugins/fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
    <script type="text/javascript" src="/admin/plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript" src="/admin/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
    <script type="text/javascript" src="/admin/plugins/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

</head>

<body class="infobar-overlay sidebar-hideon-collpase sidebar-scroll">

<header id="topnav" class="navbar navbar-midnightblue navbar-fixed-top clearfix" role="banner">

<span id="trigger-sidebar" class="toolbar-trigger toolbar-icon-bg">
	<a data-toggle="tooltips" data-placement="right" title="Toggle Sidebar">
    <span class="icon-bg">
      <i class="fa fa-fw fa-bars"></i>
    </span>
  </a>
</span>

<span id="trigger-infobar" class="toolbar-trigger toolbar-icon-bg">
 <a href="{{ route('logout') }}">
 <span class="icon-bg">
  <i class="fas fa-sign-out-alt"></i>
 </span>
</a>
</span>

<ul class="nav navbar-nav toolbar pull-right">
<li class="toolbar-icon-bg hidden-xs" id="trigger-fullscreen">
  <a href="#" class="toggle-fullscreen">
   <span class="icon-bg">
    <i class="fa fa-fw fa-arrows-alt"></i>
   </span>
  </a>
</li>
</ul>

</header>

<div id="wrapper">
 <div id="layout-static">
  <div class="static-sidebar-wrapper sidebar-inverse">
   <div class="static-sidebar">
    <div class="sidebar">
	  <div class="widget stay-on-collapse" id="widget-sidebar">
    <nav role="navigation" class="widget-body">
	   <ul class="acc-menu">
	    <li>
        <a class="home-link" href="{{ route('controlPanelHome') }}">
        <i class="fa fa-wrench" aria-hidden="true"></i>
        <span class="font-7">მომხმარებელი</span>
       </a>
      </li>

      <li>
        <a href="{{ route('useranalytics') }}">
        <i class="fas fa-chart-pie"></i>
        <span class="font-7">ანალიტიკა</span>
       </a>
      </li>

      <li>
        <a href="{{ route('userstatements') }}">
        <i class="fas fa-bullhorn"></i>
        <span class="font-7">განცხადებები</span>
       </a>
      </li>

    <li>
      <a href="{{ route('cpusercontact') }}">
        <i class="fa fa-info-circle"></i>
        <span class="font-7">კონტაქტი</span>
      </a>
    </li>

    <li class="hasChild">
      <a href="#">
        <i class="fas fa-cog"></i>
        <span class="font-7">პარამეტრები</span>
      </a>
      <ul class="acc-menu sidebar-sub-menu">
        <li>
          <a href="{{ route('paramsgeneral') }}">
           <i class="fas fa-sliders-h"></i>
           <span class="font-7">საერთო</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramslaptop') }}">
           <i class="fas fa-laptop"></i>
           <span class="font-7">ლეპტოპის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramscpu') }}">
           <i class="fas fa-microchip"></i>
           <span class="font-7">პროცესორის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsram') }}">
           <i class="fas fa-memory"></i>
           <span class="font-7">ოპერატიულის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsmtb') }}">
           <i class="flaticon-033-motherboard"></i>
           <span class="font-7">დედაპლატის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsvc') }}">
           <i class="flaticon-026-sound-card"></i>
           <span class="font-7">გრაფიკის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramshdd') }}">
           <i class="flaticon-005-hdd"></i>
           <span class="font-7">HDD მეხსიერების</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsssd') }}">
           <i class="flaticon-004-ssd"></i>
           <span class="font-7">SSD მეხსიერების</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsodd') }}">
           <i class="flaticon-001-cd-drive"></i>
           <span class="font-7">დისკმძრავების</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsmonitor') }}">
           <i class="fas fa-desktop"></i>
           <span class="font-7">მონიტორის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsncm') }}">
           <i class="fas fa-charging-station"></i>
           <span class="font-7">ნოუთბუქის დამტენის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsnd') }}">
           <i class="fas fa-project-diagram"></i>
           <span class="font-7">ქსელური აპარატურის</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsperipherals') }}">
           <i class="flaticon-012-printer-1"></i>
           <span class="font-7">პერიფერიალების</span>
          </a>
        </li>
        <li>
          <a href="{{ route('paramsacc') }}">
           <i class="flaticon-018-mouse"></i>
           <span class="font-7">აქსესუარების</span>
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="{{ route('cpuserslides') }}">
        <i class="far fa-images"></i>
        <span class="font-7">სლაიდები</span>
      </a>
    </li>
    <li>
      <a href="{{ route('cpOrders') }}">
        <i class="fas fa-layer-group" style="font-size: 20px"></i>
        <span class="font-7">შეკვეთები</span>
      </a>
    </li>
    <li>
      <a href="{{ route('warranty') }}">
        <i class="fas fa-award" style="font-size: 20px"></i>
        <span class="font-7">საგარანტიო</span>
      </a>
    </li>
    <li>
      <a href="{{ route('invoice') }}">
        <i class="fas fa-file-invoice"></i>
        <span class="font-7">ინვოისი</span>
      </a>
    </li>
    <li>
      <a href="{{ route('cpusersb') }}">
        <i class="flaticon-016-tower"></i>
        <span class="font-7">სისტემური ბლოკები</span>
      </a>
    </li>
    <li>
      <a href="{{ route('cpusertemplates') }}">
      <i class="fas fa-file-spreadsheet"></i>
      <span class="font-7">სისტემის შაბლონები</span>
     </a>
    </li>
    <li>
      <a href="{{ route('cpuserlaptops') }}">
        <i class="fas fa-laptop"></i>
        <span class="font-7">ლეპტოპები</span>
      </a>
    </li>
    <li class="hasChild">
      <a href="#">
        <i class="fas fa-tools"></i>
        <span class="font-7">ნაწილები</span>
      </a>
      <ul class="acc-menu sidebar-sub-menu">
        <li>
          <a href="{{ route('cpusercpu') }}">
           <i class="fas fa-microchip"></i>
           <span class="font-7">პროცესორები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpusermm') }}">
           <i class="fas fa-memory"></i>
           <span class="font-7">ოპერატიულები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpusermb') }}">
           <i class="flaticon-033-motherboard"></i>
           <span class="font-7">დედაპლატები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuservc') }}">
           <i class="flaticon-026-sound-card"></i>
           <span class="font-7">ვიდეო ბარათები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuserhdd') }}">
           <i class="flaticon-005-hdd"></i>
           <span class="font-7">HDD მეხსიერება</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuserssd') }}">
           <i class="flaticon-004-ssd"></i>
           <span class="font-7">SSD მეხსიერება</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuserps') }}">
           <i class="flaticon-002-supply"></i>
           <span class="font-7">კვების ბლოკები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuserpc') }}">
           <i class="flaticon-019-cooler"></i>
           <span class="font-7">CPU ქულერები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpusercc') }}">
           <i class="flaticon-015-cooler-1"></i>
           <span class="font-7">კეისის ქულერები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpusercases') }}">
           <i class="flaticon-003-tower-1"></i>
           <span class="font-7">კეისები</span>
          </a>
        </li>
        <li>
          <a href="{{ route('cpuserodd') }}">
           <i class="flaticon-001-cd-drive"></i>
           <span class="font-7">დისკმძრავები</span>
          </a>
        </li>
       </ul>
    </li>

    <li>
      <a href="{{ route('cpusermonitors') }}">
        <i class="fas fa-desktop"></i>
        <span class="font-7">მონიტორები</span>
      </a>
    </li>

    <li>
      <a href="{{ route('cpuseracc') }}">
        <i class="flaticon-018-mouse"></i>
        <span class="font-7">აქსესუარები</span>
      </a>
    </li>

    <li>
     <a href="{{ route('cpuserups') }}">
       <i class="fa fa-plug"></i>
       <span class="font-7">უწყვეტი კვების წყარო</span>
      </a>
     </li>
     <li>
      <a href="{{ route('cpusernc') }}">
        <i class="fas fa-charging-station"></i>
        <span class="font-7">ნოუთბუქის დამტენები</span>
       </a>
      </li>
    <li>
      <a href="{{ route('cpusernd') }}">
        <i class="fas fa-project-diagram"></i>
        <span class="font-7">ქსელური აპარატურა</span>
      </a>
    </li>
    <li>
      <a href="{{ route('cpuserperipherals') }}">
        <i class="flaticon-012-printer-1"></i>
        <span class="font-7">პერიფერიალები</span>
      </a>
    </li>
	   </ul>
    </nav>
   </div>
  </div>
 </div>
</div>

<div class="static-content-wrapper">
  <div class="static-content">
    <div class="page-content">
       <div class="container-fluid mt40" id="main-content">
         <!-- Page content start-->

          @yield('content')

         <!-- Page content end-->
        </div>
      </div>
    </div>

  <footer role="contentinfo">
    <div class="clearfix">
        <ul class="list-unstyled list-inline pull-left">
            <li>
              <h6 style="margin: 0"></h6>
            </li>
        </ul>
        <button class="pull-right btn btn-link btn-xs hidden-print" id="back-to-top">
          <i class="fa fa-arrow-up"></i>
        </button>
    </div>
   </footer>
  </div>
 </div>
</div>

<!--- snackbar element to display request response --->

<div id="snackbar">
 <i class="fa fa-info-circle"></i>
 <span class="message font-6"></span>
</div>

<!-- page load script -->

<script type="text/javascript" src="/admin/js/page-loader.js?v=35"></script>

<!-- Load jQuery -->

<script type="text/javascript" src="/admin/js/jqueryui-1.9.2.min.js?v=35"></script> <!-- Load jQueryUI -->
<script type="text/javascript" src="/admin/js/bootstrap.min.js?v=35"></script> 	<!-- Load Bootstrap -->

<script type="text/javascript" src="/admin/plugins/easypiechart/jquery.easypiechart.js?v=35"></script> <!-- EasyPieChart-->
<script type="text/javascript" src="/admin/plugins/sparklines/jquery.sparklines.min.js?v=35"></script> <!-- Sparkline -->
<script type="text/javascript" src="/admin/plugins/jstree/dist/jstree.min.js?v=35"></script> <!-- jsTree -->

<script type="text/javascript" src="/admin/plugins/codeprettifier/prettify.js?v=35"></script> <!-- Code Prettifier  -->
<script type="text/javascript" src="/admin/plugins/bootstrap-switch/bootstrap-switch.js?v=35"></script> <!-- Swith/Toggle Button -->

<script type="text/javascript" src="/admin/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js?v=35"></script>  <!-- Bootstrap Tabdrop -->
<script type="text/javascript" src="/admin/plugins/iCheck/icheck.min.js?v=35"></script>  <!-- iCheck -->

<script type="text/javascript" src="/admin/js/enquire.min.js?v=35"></script> <!-- Enquire for Responsiveness -->
<script type="text/javascript" src="/admin/plugins/bootbox/bootbox.js?v=35"></script>	<!-- Bootbox -->

<script type="text/javascript" src="/admin/plugins/nanoScroller/js/jquery.nanoscroller.min.js?v=35"></script> <!-- nano scroller -->
<script type="text/javascript" src="/admin/plugins/jquery-mousewheel/jquery.mousewheel.min.js?v=35"></script> 	<!-- Mousewheel support needed for jScrollPane -->

<script type="text/javascript" src="/admin/js/application.js?v=35"></script>
<script type="text/javascript" src="/admin/demo/demo.js?v=35"></script>
<script type="text/javascript" src="/admin/demo/demo-switcher.js?v=35"></script>

<!-- End loading site level scripts -->

<!-- Load page level scripts-->

<script type="text/javascript" src="/admin/plugins/form-multiselect/js/jquery.multi-select.min.js?v=35"></script> <!-- Multiselect Plugin -->
<script type="text/javascript" src="/admin/plugins/quicksearch/jquery.quicksearch.min.js?v=35"></script> <!-- Quicksearch to go with Multisearch Plugin -->
<script type="text/javascript" src="/admin/plugins/form-typeahead/typeahead.bundle.min.js?v=35"></script> <!-- Typeahead for Autocomplete -->
<script type="text/javascript" src="/admin/plugins/form-select2/select2.min.js?v=35"></script> <!-- Advanced Select Boxes -->
<script type="text/javascript" src="/admin/plugins/form-autosize/jquery.autosize-min.js?v=35"></script> <!-- Autogrow Text Area -->
<script type="text/javascript" src="/admin/plugins/form-colorpicker/js/bootstrap-colorpicker.min.js?v=35"></script> <!-- Color Picker -->
<script type="text/javascript" src="/admin/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js?v=35"></script> <!-- Touchspin -->

<!-- Fullscreen Editor -->

<script type="text/javascript" src="/admin/plugins/form-jasnyupload/fileinput.min.js?v=35"></script> <!-- File Input -->
<script type="text/javascript" src="/admin/plugins/form-tokenfield/bootstrap-tokenfield.min.js?v=35"></script>  <!-- Tokenfield -->
<script type="text/javascript" src="/admin/plugins/switchery/switchery.js?v=35"></script>  <!-- Switchery -->

<script type="text/javascript" src="/admin/plugins/card/lib/js/card.js?v=35"></script> <!-- Card -->
<script type="text/javascript" src="/admin/plugins/bootstrap-switch/bootstrap-switch.js?v=35"></script> <!-- BS Switch -->
<script type="text/javascript" src="/admin/plugins/jquery-chained/jquery.chained.min.js?v=35"></script> <!-- Chained Select Boxes -->

<script type="text/javascript" src="/admin/plugins/jquery-mousewheel/jquery.mousewheel.min.js?v=35"></script> <!-- MouseWheel Support -->
<script type="text/javascript" src="/admin/demo/demo-formcomponents.js?v=35"></script>
<script type="text/javascript" src="/admin/plugins/wijets/wijets.js?v=35"></script> <!-- Wijet -->

 <!-- End loading page level scripts-->

 </body>
</html>
