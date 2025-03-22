<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Helpdesk Pemerintah Kota Blitar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>

<body class="text-white gradient">
    <nav id="header" class="navbar navbar-expand-lg navbar-light fixed-top bg-white text-white">
        <div class="container">
            <div class="navbar-brand">
                <img id="nav-logo" src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}"
                    class="img-fluid" style="width: 150px; height: auto;">
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav-content" aria-controls="nav-content" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav-content">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-black" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black" href="#tentang">Tentang Helpdesk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black" href="#mengapa">Mengapa Helpdesk</a>
                    </li>
                </ul>
                <button class="btn btn-primary text-white fw-bold rounded-pill ms-3 mt-3 mt-lg-0 px-4 py-2 shadow" onclick="window.location.href='{{ route('login') }}'">
                    Login
                </button>
            </div>
        </div>
    </nav>

    <section id="beranda" class="pt-5">
        <div id="particles-js"></div>
        <div class="container px-3 py-5" style="margin-top: 90px;">
            <div class="row align-items-center">
                <div class="col-12 text-center py-10">
                    <p class="text-uppercase tracking-widest" data-aos="fade-down" data-aos-duration="3000">Selamat Datang di Website Layanan Aduan & Informasi</p>
                    <h1 class="display-4 fw-bold my-4" data-aos="zoom-in" data-aos-duration="3000">HELPDESK PEMERINTAH KOTA BLITAR</h1>
                    <p class="fs-3 mb-5" data-aos="fade-up" data-aos-duration="3000">Laporkan masalah Anda dengan cepat, dapatkan solusi tepat, dan akses layanan publik dengan mudah.</p>
                </div>
            </div>
        </div>
        <div class="position-relative">
            <svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
                        <path d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496" opacity="0.100000001"></path>
                        <path d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z" opacity="0.100000001"></path>
                        <path d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z" id="Path-4" opacity="0.200000003"></path>
                    </g>
                    <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
                        <path d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z"></path>
                    </g>
                </g>
            </svg>
        </div>
    </section>

    <section class="bg-white border-bottom py-5" id="tentang">
        <div class="container px-4" style="margin-top: 70px;">
            <h2 class="display-4 fw-bold text-center text-dark my-3" data-aos="fade-down" data-aos-duration="1000">Tentang Helpdesk</h2>
            <div class="mx-auto w-25 my-3" style="height: 10px; background-color: #1572e8 ;"></div>
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-12 col-md-6 p-4" data-aos="fade-right" data-aos-duration="1000">
                    <h3 class="h3 fw-bold text-dark mb-3">Helpdesk Pemerintah Kota Blitar</h3>
                    <p class="text-muted">
                        Helpdesk Pemerintah Kota Blitar adalah sistem layanan berbasis teknologi yang <b>berfungsi</b> sebagai pusat dukungan bagi masyarakat dan pegawai pemerintahan dalam menangani berbagai aduan, permintaan informasi, serta bantuan teknis terkait layanan publik.
                        <br><br>
                        Helpdesk ini <b>bertujuan</b> untuk meningkatkan responsivitas, transparansi, dan efisiensi dalam pelayanan publik dengan menyediakan jalur komunikasi yang mudah diakses. Masyarakat dapat menyampaikan keluhan atau bertanya tentang layanan pemerintah.
                    </p>
                </div>
                <div class="col-12 col-md-6 p-4 text-center text-md-end">
                    <img src="{{ asset('assets/media/img/bg-auth.png') }}" alt="Gambar Helpdesk" class="img-fluid mx-auto d-block" style="max-width: 70%;" data-aos="fade-left" data-aos-duration="1000">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 text-center" id="mengapa">
        <div class="container py-4">
            <h2 class="display-4 fw-bold text-dark my-3" data-aos="fade-down" data-aos-duration="1000" style="color:rgb(255, 255, 255) !important;">Mengapa Menggunakan Helpdesk?</h2>
            <div class="mx-auto w-25 my-3" style="height: 10px; background-color: #1572e8 ;"></div><br><br>
            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow" data-aos="fade-right" data-aos-duration="2000">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">Dukungan di Mana Saja</h5>
                            <p class="card-text text-muted">Sampaikan aduan Anda kapan saja, di mana saja, dengan cara yang paling nyaman bagi Anda.</p>
                        </div>
                        <div class="card-footer border-0 bg-white">
                            <img src="{{ asset('assets/media/img/dukungan.png') }}" style="width: 300px; height: 300px;">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow" data-aos="fade-up" data-aos-duration="2000">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">Komitmen Pelayanan</h5>
                            <p class="card-text text-muted">Berkomitmen untuk meningkatkan kualitas pelayanan publik, memberikan solusi yang adil, cepat, dan sesuai kebutuhan masyarakat.</p>
                        </div>
                        <div class="card-footer border-0 bg-white">
                            <img src="{{ asset('assets/media/img/komitmen.png') }}" style="width: 300px; height: 300px;">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow" data-aos="fade-left" data-aos-duration="2000">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark">Didukung Tim Profesional</h5>
                            <p class="card-text text-muted">Terdiri dari operator dan petugas teknis berpengalaman yang siap menangani aduan.</p>
                        </div>
                        <div class="card-footer border-0 bg-white">
                            <img src="{{ asset('assets/media/img/tim.png') }}" style="width: 300px; height: 300px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-white py-5">
        <div class="card-account text-center py-5 mb-5 text-dark">
            <h2 class="display-4 fw-bold my-3" data-aos="fade-down" data-aos-duration="3000">Buat Akun Sekarang</h2>
            <div class="mx-auto w-25 my-3" style="height: 10px; background-color: #1572e8;" data-aos="fade-down" data-aos-duration="3000"></div>
            <h3 class="h3 my-4" data-aos="zoom-in" data-aos-duration="3000">Punya keluhan? Buat akun dan sampaikan aduan Anda hari ini!</h3>
            <button class="btn btn-primary text-white fw-bold rounded-pill my-4 px-4 py-2 shadow" onclick="window.location.href='{{ route('register') }}'" data-aos="fade-up" data-aos-duration="3000" style="width: 10cm; height: 2cm; font-size: 25px;">
                Buat Akun
            </button>
        </div>
    </section>

    <footer style="background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);">
        <svg class="wave-top" viewBox="0 0 1439 147" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g transform="translate(-1.000000, -14.000000)" fill-rule="nonzero">
                    <g class="wave" fill="#f8fafc">
                        <path d="M1440,84 C1383.555,64.3 1342.555,51.3 1317,45 C1259.5,30.824 1206.707,25.526 1169,22 C1129.711,18.326 1044.426,18.475 980,22 C954.25,23.409 922.25,26.742 884,32 C845.122,37.787 818.455,42.121 804,45 C776.833,50.41 728.136,61.77 713,65 C660.023,76.309 621.544,87.729 584,94 C517.525,105.104 484.525,106.438 429,108 C379.49,106.484 342.823,104.484 319,102 C278.571,97.783 231.737,88.736 205,84 C154.629,75.076 86.296,57.743 0,32 L0,0 L1440,0 L1440,84 Z"></path>
                    </g>
                    <g transform="translate(1.000000, 15.000000)" fill="#FFFFFF">
                        <g transform="translate(719.500000, 68.500000) rotate(-180.000000) translate(-719.500000, -68.500000) ">
                            <path d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496" opacity="0.100000001"></path>
                            <path d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z" opacity="0.100000001"></path>
                            <path d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z" opacity="0.200000003"></path>
                        </g>
                    </g>
                </g>
            </g>
        </svg>
        <div class="container text-white py-5">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <img src="{{ asset('assets/media/img/logo-helpdesk-1.png') }}" alt="Logo Helpdesk" class="img-fluid mb-3" style="width: 300px; height: auto;">
                    <p>Sistem Layanan Bantuan dan Dukungan Pemerintah Kota Blitar</p>
                    <!-- <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-tiktok"></i></a>
                    </div> -->
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="fw-semibold mb-3">Link Terkait</h5>
                    <a href="https://blitarkota.go.id/" class="text-white text-decoration-none">Pemerintah Kota Blitar</a> <br><br>
                    <a href="https://diskominfotik.blitarkota.go.id/" class="text-white text-decoration-none">DISKOMINFOTIK Kota Blitar</a>

                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-semibold mb-3">Kontak</h5>
                    <p>Jl. Dr. Moh.Hatta Nomor 05 - Kota Blitar</p>
                    <p>Telp. 0342807805</p>
                </div>
            </div>
            <div class="text-start border-top pt-3">
                <p class="mb-0 text-center">© 2025 Helpdesk Pemerintah Kota Blitar</p>
            </div>
        </div>
    </footer>
    <style>
        .gradient {
            background: linear-gradient(90deg, #1572e8 0%, rgb(21, 68, 144) 100%);
        }

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

        #header {
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background-color: transparent !important;
            border-bottom: 3px solid #1572e8;
        }

        #header .nav-link {
            color: white !important;
            transition: color 0.3s ease-in-out;
        }

        #header.scrolled .nav-link {
            color: black !important;
        }

        #header .nav-link:hover,
        #header.scrolled .nav-link:hover {
            color: #1572e8 !important;
        }

        #header.scrolled {
            background-color: white !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-link:hover {
            color: #1572e8 !important;
        }

        .btn-primary:hover {
            color: #1572e8 !important;
            background-color: #f8f9fa;
        }

        .card:hover {
            transform: scale(1.05) !important;
            border-color: #1572e8;
            z-index: 10;
        }

        .row {
            overflow: visible;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 30%;
            top: 60%;
            left: 0;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <script>
        AOS.init();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var navbar = document.getElementById("header");

            window.addEventListener("scroll", function() {
                if (window.scrollY > 50) {
                    navbar.classList.add("scrolled");
                } else {
                    navbar.classList.remove("scrolled");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            var navbar = document.getElementById("header");
            var logo = document.getElementById("nav-logo");

            window.addEventListener("scroll", function() {
                if (window.scrollY > 50) {
                    navbar.classList.add("scrolled");
                    logo.src = "{{ asset('assets/media/img/logo-helpdesk-black.png') }}";
                } else {
                    navbar.classList.remove("scrolled");
                    logo.src = "{{ asset('assets/media/img/logo-helpdesk-1.png') }}";
                }
            });
        });

        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 400,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: "#ffffff"
                },
                shape: {
                    type: "circle"
                },
                opacity: {
                    value: 0.5,
                    random: false
                },
                size: {
                    value: 1,
                    random: true
                },
                move: {
                    enable: true,
                    speed: 0.8,
                    direction: "none",
                    out_mode: "out"
                }
            },
        });
    </script>
</body>

</html>