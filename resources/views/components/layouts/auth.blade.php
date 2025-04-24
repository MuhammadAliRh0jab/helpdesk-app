<x-layouts.master title="Helpdesk Pemerintah Kota Blitar">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            font-family: 'Poppins', sans-serif;
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

        .header-logo {
            max-width: 90%;
            max-height: 90%;
        }

        @media (max-width: 1500px) {
            .header-logo {
                max-width: 90%;
                max-height: 90%;
                margin-top: 40px;
            }
        }
        @media (max-width: 991px) {
            .header-logo {
                max-width: 80%;
                max-height: 80%;
                margin-top: 40px;
                margin-left: 30px;
            }
        }
    </style>

    <div class="d-flex flex-column flex-root app-root bgi-size-cover bgi-position-center" id="kt_app_root" style="background-image: url({{ image('img/bg-auth-1.png') }})">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">
                        {{ $slot }}
                    </div>
                </div>
                <div class="d-flex flex-center flex-wrap px-5"></div>
            </div>
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" style="background-image: url({{ image('img/bg-auth.png') }})">
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <a href="{{ route('landing') }}" class="mb-12">
                        <img class="header-logo" alt="Logo" src="{{ image('img/logo-helpdesk-black.png') }}" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.master>