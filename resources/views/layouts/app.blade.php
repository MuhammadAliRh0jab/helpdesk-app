    <!DOCTYPE html>
    <html lang="en"
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Helpdesk Pemerintah Kota Blitar - @yield('title')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="app-default">
        @include('layouts.partials.sidebar-layout.header._navbar')
        <div class="content p-6">
            @yield('content')
        </div>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        @yield('scripts')
    </body>
    </html>