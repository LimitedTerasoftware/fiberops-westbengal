<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ isset($sitetitle) ? ucfirst($sitetitle) : "Visitor-Pass" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="keywords" content="Davangere,Davangere Smart City Limited,davangere,smartcity,Smart City,Davangere Smart City,Smartnet Library,Learning Center,E Learning Center" >

  
   <meta name="description" content="Devangere Smart City E-Learning Center Visiter Pass.">
   
   <meta content="Devangere Smart City E-Learning Center Visiter Pass." property="og:title" />
     <!-- Favicons -->
    <link rel="shortcut icon" href="{{ asset('images/site_logo.png') }}" type="image/x-icon"/>
    <link href="{{ asset('images/site_logo.png') }}" rel="icon">
    <link href="{{ asset('images/site_logo.png') }}" rel="apple-touch-icon">
    <link rel="canonical" href="https://epass.nftplaza.tools/"/>



    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/@fortawesome/fontawesome-free/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/izitoast/dist/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/aos/aos.css') }}">
    @yield('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">




</head>
