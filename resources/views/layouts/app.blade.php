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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

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
                margin-top: 30px;
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

            .card-header {
                width: 100%;
                height: 5rem;
                padding: 0.5rem 2rem;
                background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);
                border-radius: 20px !important;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header p {
                margin-top: -10px;
            }
            .card{
                background-color:rgba(21, 113, 232, 0.1);
            }
        </style>
    </body>

    </html>