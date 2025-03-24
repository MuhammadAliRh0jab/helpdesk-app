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
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/media/img/logo.png') }}">

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
                margin-left: 130px;
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
        </style>
        <style>
            #header {
                transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
                background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);
                border-bottom: 3px solid rgba(9, 9, 9, 0.17);
            }

            .navbar-toggler {
                border: none;
                background: transparent;
                font-size: 1.5rem;
                padding: 0.5rem;
            }

            .navbar-toggler:focus {
                outline: none;
            }

            @media (min-width: 990px) {
                .sidebar {
                    display: block !important;
                }

                .navbar-toggler {
                    display: none;
                }

                #main-content {
                    margin-left: 250px;
                }
            }

            @media (max-width: 991px) {

                html,
                body {
                    margin-top: 40px !important;
                    margin-left: 0 !important;
                    padding: 10px !important;
                    font-size: 12px !important;
                }

                .sidebar {
                    width: 0;
                    transform: translateX(-100%);
                    transition: all 0.3s ease;
                    display: block;
                }

                .sidebar.active {
                    width: 52%;
                    transform: translateX(0);
                }

                #main-content {
                    margin-left: 0 !important;
                }

                .sidebar-content {
                    width: 100%;
                }
            }

            .sidebar {
                height: 100%;
                width: 250px;
                position: fixed;
                z-index: 1000;
                top: 0;
                left: 0;
                background-color: white;
                overflow-x: hidden;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            }

            .sidebar-content {
                padding: 20px;
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .sidebar-header {
                padding-bottom: 20px;
                border-bottom: 1px solid #e9ecef;
            }

            .nav-item {
                padding-bottom: 10px;
            }

            .nav-link {
                color: #000 !important;
                padding: 10px 15px;
                transition: all 0.3s ease;
            }

            .nav-link:hover {
                background-color: rgba(0, 0, 0, 0.08);
                color: #1572e8 !important;
                border-radius: 5px;
            }

            .sidebar-footer {
                padding-top: 20px;
            }

            .btn-danger {
                transition: all 0.3s ease;
                width: 80%;
            }

            .btn-danger:hover {
                background-color: rgb(255, 255, 255);
                color: red !important;
            }

            .nav-item i {
                font-size: 16px;
                color: #1572e8;
            }

            #main-content {
                margin-left: 250px;
                padding: 20px;
                transition: none;
            }

            .card-title {
                color: rgba(180, 180, 180, 0.8);
                font-size: 16px;
            }

            .card {
                border-color: #1572e8;
            }

            .card-body i {
                border-right: 1px solid #1572e8;
                padding-right: 15px;
                align-content: center;
            }

            .fa-solid {
                font-size: 60px;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');

                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });

                document.addEventListener('click', function(event) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickToggle = sidebarToggle.contains(event.target);

                    if (!isClickInsideSidebar && !isClickToggle && window.innerWidth < 992) {
                        sidebar.classList.remove('active');
                    }
                });
            });
        </script>
    </body>

    </html>