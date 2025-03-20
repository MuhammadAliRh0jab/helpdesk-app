    <!DOCTYPE html>
    <html lang="en"
        <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk Pemerintah Kota Blitar - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->

    </head>

    <body class="app-default">
        @include('layouts.partials.sidebar-layout.header._navbar')
        <div class="content p-6">
            @yield('content')
        </div>
        @yield('scripts')
        <style>
            html,
            body {
                font-family: 'Poppins', sans-serif;
                margin-top: 50px;
                padding: 20px;
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            p,
            a,
            button,
            input,
            textarea {
                font-family: 'Poppins', sans-serif;
            }

        </style>
    </body>

    </html>