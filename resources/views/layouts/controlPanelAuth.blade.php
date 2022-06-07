<!DOCTYPE html>
<html lang="ka">
  <head>
    <!-- Meta, title, CSS -->
    <title>@yield('title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- CSRF Token -->
    <meta name="csrftkn" content="{{ csrf_token() }}">
    <!-- Font Awesome -->
    <link href="/admin/fonts/font-awesome-pro/css/pro.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="/admin/css/auth.css?v=4" rel="stylesheet">
    <!--- Company logo --->
    <link rel="icon" href="/images/general/logo.ico">
    <!--- Vendros scripts --->
    <script type="text/javascript" src="/vendor/axios/axios.min.js?v=40"></script>
    <script type="text/javascript" src="/js/validation.js?v=40"></script>
  </head>
  <body class="page">
    <!-- Page content start-->
    @yield('content')
    <!-- Page content end-->
  </body>
</html>
