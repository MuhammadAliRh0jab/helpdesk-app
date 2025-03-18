<div class="header-area" id="headerArea">
    <div class="container">
        <!-- Header Content -->
        <div class="header-content header-style-three position-relative d-flex align-items-center justify-content-between">

            <div class="back-button">
                <a href="{{ URL::previous() }}">
                    <i class="bi bi-arrow-left-short"></i>
                </a>
            </div>

            <div class="logo-wrapper">
                {{-- <a href="{{ route('mobile.home') }}"> --}}
                <a href="{{ route('tickets.index') }}">
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
