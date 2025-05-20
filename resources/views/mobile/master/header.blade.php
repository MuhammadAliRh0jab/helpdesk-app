<div class="header-area" id="headerArea">
    <div class="container">
        <div
            class="header-content header-style-three position-relative d-flex align-items-center justify-content-between">
            <div class="navbar--toggler" id="affanNavbarToggler4" data-bs-toggle="offcanvas"
                data-bs-target="#affanOffcanvas" aria-controls="affanOffcanvas">
                <div class="span-wrap">
                    <span class="d-block"></span>
                    <span class="d-block"></span>
                    <span class="d-block"></span>
                </div>
            </div>
            <div class="logo-wrapper">
                <a href="{{ route('dashboard.warga') }}">
                    <img class="logo-img" src="{{ asset('mobile/img/core-img/logo.png') }}" alt="Logo">
                </a>
            </div>
            <div class="user-profile-wrapper">
                <a class="user-profile-trigger-btn" href="#">
                    <img src="{{ asset('mobile/img/bg-img/2.jpg') }}" alt="">
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoImg = document.querySelector('.logo-img');
            const htmlElement = document.documentElement;

            function updateLogo() {
                if (htmlElement.getAttribute('data-theme') === 'dark') {
                    logoImg.src = "{{ asset('mobile/img/core-img/logo-dark.png') }}";
                } else {
                    logoImg.src = "{{ asset('mobile/img/core-img/logo.png') }}";
                }
            }

            // Perbarui logo saat halaman dimuat
            updateLogo();

            // Pantau perubahan tema
            const observer = new MutationObserver(updateLogo);
            observer.observe(htmlElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });
        });
    </script>
@endsection
