<div class="footer-nav-area" id="footerNav">
    <div class="container px-0">
        <div class="footer-nav position-relative footer-style-six">
            <ul class="h-100 d-flex align-items-center justify-content-between ps-0">
                <li @if(request()->routeIs('dashboard.warga')) class="active" @endif>
                    <a href="{{ route('dashboard.warga') }}">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li @if(request()->routeIs('tickets.index')) class="active" @endif>
                    <a href="{{ route('tickets.index') }}">
                        <i class="bi bi-ticket"></i>
                        <span>Tiket</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="bi bi-person"></i>
                        <span>Akun</span>
                    </a>
                </li>
                {{-- <li @if(request()->routeIs('mobile.account')) class="active" @endif>
                    <a href="{{ route('mobile.account') }}">
                        <i class="bi bi-person"></i>
                        <span>Akun</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
</div>
