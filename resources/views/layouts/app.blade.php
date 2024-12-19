<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @include('layouts.include.link')
    </head>

    <body id="page-top">
        <div id="wrapper">
            @include('layouts.include.navigation')
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
       @include('layouts.include.modal')

    @include('layouts.include.script')
    </body>
</html>
