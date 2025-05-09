@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
  <main id="main-container">
    <div class="content">
      <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
        <div class="flex-grow-1 mb-1 mb-md-0">
          <h1 class="h3 fw-bold mb-2">
            Dashboard
          </h1>
          <h2 class="h6 fw-medium fw-medium text-muted mb-0">
            Selamat Datang <a class="fw-semibold" href="be_pages_generic_profile.html">{{ Auth::user()->name }}</a>, &#128075;&#128522; !
          </h2>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="row items-push">
        <div class="col-sm-6 col-xxl-6">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['resolved'] }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Yang Diselesaikan</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="fa fa-chart-bar fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xxl-6">
          <div class="block block-rounded d-flex flex-column h-100 mb-0">
            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
              <dl class="mb-0">
                <dt class="fs-3 fw-bold">
                  {{ $ticketStats['created'] }}
                </dt>
                <dd class="fs-sm fw-medium text-muted mb-0">Tiket Yang Diajukan</dd>
              </dl>
              <div class="item item-rounded-lg bg-body-light">
                <i class="far fa-paper-plane fs-3 text-primary"></i>
              </div>
            </div>
            <div class="bg-body-light rounded-bottom">
              <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)">
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xl-12 col-xxl-12 d-flex flex-column">
          <div class="block block-rounded flex-grow-1 d-flex flex-column">
            <div class="block-header block-header-default">
              <h3 class="block-title">Statistik Aduan</h3>
              <div class="block-options">
                <button type="button" class="btn-block-option" data-toggle="block-option"
                  data-action="state_toggle" data-action-mode="demo">
                  <i class="si si-refresh"></i>
                </button>
              </div>
            </div>
            <div class="block-content block-content-full flex-grow-1 d-flex align-items-center">
              <canvas id="statistic"></canvas>
            </div>
            @php
            $created = $ticketStats['created'] ?? 0;
            $resolved = $ticketStats['resolved'] ?? 0;
            $percentage = $created > 0 ? round(($resolved / $created) * 100, 1) : 0;
            @endphp

            <div class="block-content bg-body-light">
              <div class="row items-push text-center w-100">
                <div class="col-sm-6">
                  <dl class="mb-0">
                    <dt class="fs-3 fw-bold d-inline-flex align-items-center space-x-2">
                      {{ $created }}
                    </dt>
                    <dd class="fs-sm fw-medium text-muted mb-0">Jumlah Tiket Yang Diajukan</dd>
                  </dl>
                </div>
                <div class="col-sm-6">
                  <dl class="mb-0">
                    <dt class="fs-3 fw-bold d-inline-flex align-items-center space-x-2">
                      {{ $percentage/$resolved }}%
                    </dt>
                    <dd class="fs-sm fw-medium text-muted mb-0">Aduan Diselesaikan</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </main>
</div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const total = Number({{ 
        ($ticketStats['assigned'] ?? 0) + 
        ($ticketStats['pending'] ?? 0) + 
        ($ticketStats['resolved'] ?? 0) 
    }});

    const resolved = Number({{ $ticketStats['resolved'] ?? 0 }});

    const percentageResolved = total > 0 ? ((resolved / total) * 100).toFixed(1) : 0;

    const ctx = document.getElementById('statistic').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Selesai', 'Dalam Proses'],
            datasets: [{
                label: 'Persentase Aduan',
                data: [percentageResolved, (100 - percentageResolved).toFixed(1)],
                backgroundColor: ['#1d4ed8	', '#487fff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
});

</script>


@endsection