@extends('mobile.master.app')

@section('title', 'Selamat Datang')

@section('content')
    <div>
        <!-- Hero Block Wrapper -->
        <div class="hero-block-wrapper bg-primary">
            <!-- Styles -->
            <div class="hero-block-styles">
                <div class="hb-styles1" style="background-image: url('{{ asset('mobile/img/core-img/dot.png') }}')"></div>
                <div class="hb-styles2"></div>
                <div class="hb-styles3"></div>
            </div>

            <div class="custom-container">
                <div class="carousel slide carousel-fade" id="bootstrapCarouselFade" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button class="active" type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="0"
                            aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#bootstrapCarouselFade" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>

                    <!-- Carousel Inner -->
                    <div class="carousel-inner text-center">
                        <div class="carousel-item active">
                            <div class="hero-block-content">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img class="d-block" src="{{ asset('mobile/img/bg-img/img-1.png') }}" alt="">
                                    <h2 class="display-4 text-white my-3">Apa itu Helpdesk Kota Blitar?</h2>
                                    <p class="text-white">Helpdesk Kota Blitar adalah layanan bantuan untuk menjawab
                                        pertanyaan dan menyelesaikan permasalahan masyarakat maupun ASN terkait layanan
                                        kota. Pengguna dapat mengajukan pertanyaan dengan akun untuk mendapatkan akses.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-block-content">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <img class="d-block" src="{{ asset('mobile/img/bg-img/img-2.png') }}" alt="">
                                    <h2 class="display-4 text-white my-3">Mengapa Helpdesk Kota Blitar?</h2>
                                    <p class="text-white">Kami hadir untuk memberikan solusi cepat, mudah, dan terpercaya
                                        guna memenuhi kebutuhan informasi serta mendukung kelancaran layanan bagi seluruh
                                        masyarakat dan ASN Kota Blitar.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="hero-block-content">
                                <h2 class="display-4 text-white mb-3">Satu Langkah Lagi!</h2>
                                <p class="text-white">Segera mulai dengan Helpdesk Kota Blitar dan nikmati kemudahan akses
                                    layanan serta jawaban cepat untuk setiap pertanyaan Anda - solusi terbaik hanya dalam
                                    satu klik!</p>
                                <a class="btn btn-warning btn-lg w-100 mb-3" href="{{ route('dashboard.warga') }}">Mulai
                                    Sekarang</a>
                                <a class="btn btn-warning btn-lg w-100" href="{{ route('guest') }}">Lapor Sebagai
                                    Tamu</a>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Control Prev -->
                    <button class="carousel-control-prev" data-bs-target="#bootstrapCarouselFade" hidden type="button"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>

                    <!-- Carousel Control Next -->
                    <button class="carousel-control-next" data-bs-target="#bootstrapCarouselFade" hidden type="button"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
@endsection
