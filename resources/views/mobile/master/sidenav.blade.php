<div class="offcanvas offcanvas-start" id="affanOffcanvas" data-bs-scroll="true" tabindex="-1" aria-labelledby="affanOffcanvsLabel">
    <button class="btn-close btn-close-white text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    <div class="offcanvas-body p-0">
        <div class="sidenav-wrapper">
            <div class="sidenav-profile bg-gradient">
                <div class="sidenav-style1"></div>
                <div class="user-profile">
                    <img src="{{ asset('mobile/img/bg-img/2.jpg') }}" alt="">
                </div>
                <div class="user-info">
                    <h6 class="user-name mb-0">{{ Auth::user()->name }}</h6>
                </div>
            </div>
            <ul class="sidenav-nav ps-0">
                <li @if(request()->routeIs('dashboard.warga')) class="active" @endif>
                    <a href="{{ route('dashboard.warga') }}">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#"><i class="bi bi-ticket"></i> Ticket</a>
                    <ul>
                      <li>
                        <a href="{{ route('tickets.index') }}"> All</a>
                      </li>
                      <li>
                        {{-- <a href="{{ route('mobile.ticket.pending') }}"> Belum Direspon</a> --}}
                        <a href="#"> Belum Direspon</a>
                      </li>
                      <li>
                        {{-- <a href="{{ route('mobile.ticket.assigned') }}"> Direspon</a> --}}
                        <a href="#"> Direspon</a>
                      </li>
                      <li>
                        {{-- <a href="{{ route('mobile.ticket.resolve') }}"> Selesai</a> --}}
                        <a href="#"> Selesai</a>
                      </li>
                    </ul>
                  </li>
                {{-- <li @if(request()->routeIs('mobile.account')) class="active" @endif> --}}
                <li>
                    <a href="#">
                        <i class="bi bi-person"></i> Akun
                    </a>
                    {{-- <a href="{{ route('mobile.account') }}">
                        <i class="bi bi-person"></i> Akun
                    </a> --}}
                </li>
                <li>
                    <div class="night-mode-nav">
                        <i class="bi bi-moon"></i> Night Mode
                        <div class="form-check form-switch">
                            <input class="form-check-input form-check-success" id="darkSwitch" type="checkbox">
                        </div>
                    </div>
                </li>
                <li><a href="login.html"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>
