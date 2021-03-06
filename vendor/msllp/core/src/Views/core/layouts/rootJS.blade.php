<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<link rel="dns-prefetch" href="{{env('APP_URL')}}"/>
<head>
    <meta charset="utf-8"/>
    <title>MS Frame</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width,height=device-height,, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="Million Solutions LLP" name="author"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset("b/css/app.css") }}?{{\MS\Core\Helper\Comman::random(10)}}" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <meta name="theme-color" content="#db5945">


</head>
<body class="min-vh-100" >


<div id="msapp">
    @yield('body')
</div>
<script src="{{ asset("b/js/app.js") }}?{{\MS\Core\Helper\Comman::random(10)}}" type="text/javascript" defer></script>
<script>@yield('js')</script>
</body>
</html>
