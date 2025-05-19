<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path fill='%234e73df' d='M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM96.8 314.1c-3.8-13.7 7.4-26.1 21.6-26.1H393.6c14.2 0 25.5 12.4 21.6 26.1C396.2 382 332.1 432 256 432s-140.2-50-164.8-117.9zM144.4 192a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm192-32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z'/></svg>"
        type="image/svg+xml">
    <title>@yield('title')</title>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/bootstrap.min.css') }}" />

    <!-- Slick -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/slick.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/slick-theme.css') }}" />

    <!-- nouislider -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/nouislider.min.css') }}" />

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="{{ asset('asset/guest/css/font-awesome.min.c') }}ss">

    <!-- Custom stlylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('asset/guest/css/style.css') }}" />

</head>

<!-- HEADER -->
@include('layouts.include.guest.header')
<!-- /HEADER -->

<!-- NAVIGATION -->
@include('layouts.include.guest.navigation')
<!-- /NAVIGATION -->
<!-- CONTENT -->
{{ $slot }}
<!-- /CONTENT -->

<!-- FOOTER -->
@include('layouts.include.guest.footer')
<!-- /FOOTER -->

<script src="{{ asset('asset/guest/js/jquery.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/slick.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/nouislider.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/jquery.zoom.min.js') }}"></script>
<script src="{{ asset('asset/guest/js/main.js') }}"></script>


</html>
