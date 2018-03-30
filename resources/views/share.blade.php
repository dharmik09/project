<!DOCTYPE html>
<?php 
    $metaTitle = $_GET['title'];
    $metaDescription = $_GET['description'];
    $siteUrl = $_GET['url'];
    $shareImageUrl = $_GET['image'];
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-32x32.png')}}" sizes="32x32" />
        <link rel="icon" type="image/png" href="{{ asset('/frontend/images/favicon-16x16.png')}}" sizes="16x16" />
        <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">
        <link href="{{asset('css/owl.css')}}" rel="stylesheet">
        <link href="{{asset('css/magnific-popup.css')}}" rel="stylesheet">
        <link href="{{asset('css/aos.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
        <title>{{ $metaTitle }}</title>
        <meta name="title" content="{{$metaTitle}}" />
        <meta name="description" content="{{$metaDescription}}" />
        <meta name="keywords" content="{{$metaTitle}}" />
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@ProTeenLife" />
        <meta name="twitter:title" content="{{$metaTitle}}" />
        <meta name="twitter:description" content="{{$metaDescription}}" />
        <meta name="twitter:creator" content="@ProTeenLife" />
        <meta name="twitter:image"  content="{{$shareImageUrl}}"  />
        <!-- Facebook Card data -->
        <meta property="fb:app_id" content="1899859370300984" />
        <meta property="og:title" content="{{$metaTitle}}" />
        <meta property="og:type" content="deal" />
        <meta property="og:url" content="{{$siteUrl}}" />
        <meta property="og:image"  content="{{$shareImageUrl}}"  />
        <meta property="og:description" content="{{$metaDescription}}" />
        <meta property="og:site_name" content="ProTeenLife" />
    </head>
    <body class="fixed-nav {{ (Route::getFacadeRoot()->current()->uri() == 'teenager/signup') ? 'sec-overflow' : '' }}">
        You will redirect on {{ $siteUrl }}
    </body>
</html>