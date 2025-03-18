<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Affan - PWA Mobile HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0134d4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Affan - PWA Mobile HTML Template</title>
    <link rel="icon" href="{{ asset('mobile/img/core-img/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('mobile/img/icons/icon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('mobile/img/icons/icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="167x167" href="{{ asset('mobile/img/icons/icon-167x167.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('mobile/img/icons/icon-180x180.png') }}">
    <link rel="stylesheet" href="{{ asset('mobile/css/style.css') }}">
    <link rel="manifest" href="{{ asset('mobile/manifest.json') }}">
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Internet Connection Status -->
    <div class="internet-connection-status" id="internetStatus"></div>

    <!-- Header Area (Optional) -->
    @yield('header')

    <!-- Sidenav Left (Optional) -->
    @yield('sidenav')

    <!-- Page Content (Required) -->
    @yield('content')

    <!-- Footer Nav (Optional) -->
    @yield('footer')

    @yield('scripts')

    <!-- All JavaScript Files -->
    <script src="{{ asset('mobile/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('mobile/js/slideToggle.min.js') }}"></script>
    <script src="{{ asset('mobile/js/internet-status.js') }}"></script>
    <script src="{{ asset('mobile/js/tiny-slider.js') }}"></script>
    <script src="{{ asset('mobile/js/venobox.min.js') }}"></script>
    <script src="{{ asset('mobile/js/countdown.js') }}"></script>
    <script src="{{ asset('mobile/js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('mobile/js/vanilla-dataTables.min.js') }}"></script>
    <script src="{{ asset('mobile/js/index.js') }}"></script>
    <script src="{{ asset('mobile/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('mobile/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('mobile/js/dark-rtl.js') }}"></script>
    <script src="{{ asset('mobile/js/pswmeter.js') }}"></script>
    <script src="{{ asset('mobile/js/active.js') }}"></script>
    <script src="{{ asset('mobile/js/pwa.js') }}"></script>
</body>
</html>
