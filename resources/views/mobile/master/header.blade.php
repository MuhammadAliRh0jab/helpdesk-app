<div class="header-area" id="headerArea">
    <div class="container">
        <div class="header-content header-style-three position-relative d-flex align-items-center justify-content-between">
            <div class="navbar--toggler" id="affanNavbarToggler4" data-bs-toggle="offcanvas" data-bs-target="#affanOffcanvas" aria-controls="affanOffcanvas">
                <div class="span-wrap">
                    <span class="d-block"></span>
                    <span class="d-block"></span>
                    <span class="d-block"></span>
                </div>
            </div>
            <div class="logo-wrapper">
                {{-- <a href="{{ route('mobile.home') }}"> --}}
                <a href="{{ route('dashboard.warga') }}">
                    <img src="{{ asset('mobile/img/core-img/logo.png') }}" alt="">
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
