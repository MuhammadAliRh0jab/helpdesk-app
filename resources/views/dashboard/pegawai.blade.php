@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h6><strong>Dashboard</strong></h6> <hr><br>
        <h6 class="card-title- mb-2 fs-5">Selamat Datang, <strong>{{ Auth::user()->name }}</strong> &#128075;&#128522; !</h6>
            <p>Sebagai Pegawai, Anda dapat melihat tiket yang Anda ajukan dan tiket yang telah Anda selesaikan. Gunakan dashboard ini untuk memantau kontribusi Anda dalam penyelesaian aduan.</p>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-6">
        <div class="flex-column align-items-center justify-content-center" style="margin-top: -20px;">
          <canvas id="ticketChart"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="row g-3">
          <div class="col-md-12">
            <div class="card-custom card-blue">
              <div class="card-body d-flex justify-content-between align-items-center">
                <i class="fa-solid fa-ticket"></i>
                <div class="d-flex flex-column text-end">
                  <h6 class="card-title">Tiket Yang Diajukan</h6>
                  <p class="fs-2 text-white">{{ $ticketStats['created'] }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card-custom card-darkblue">
              <div class="card-body d-flex justify-content-between align-items-center">
                <i class="fa-solid fa-briefcase"></i>
                <div class="d-flex flex-column text-end">
                  <h6 class="card-title">Tiket Yang Diselesaikan</h6>
                  <p class="fs-2 text-white">{{ $ticketStats['resolved'] }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <hr>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ticketStats = {
        created: {{ $ticketStats['created'] ?? 0 }},
        resolved: {{ $ticketStats['resolved'] ?? 0 }}
    };

    const chartOptions = {
        type: 'doughnut',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'left',
                    labels: {
                        color: '#111',
                        boxWidth: 15,
                        padding: 40
                    }
                }
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const ticketChartCtx = document.getElementById('ticketChart');
        if (ticketChartCtx) {
            new Chart(ticketChartCtx, {
                ...chartOptions,
                data: {
                    labels: ['Diajukan', 'Diselesaikan'],
                    datasets: [{
                        label: 'Jumlah Tiket',
                        data: [ticketStats.created, ticketStats.resolved],
                        backgroundColor: ['#3E64A9', '#20358A'],
                        borderWidth: 1,
                        radius: '80%'
                    }]
                }
            });
        }
    });
</script>

<style>
  .card-custom {
    border-radius: 20px;
    color: white;
    padding: 20px;
  }

  .card-blue {
    background-color: #3E64A9;
  }

  .card-purple {
    background-color: #6D5DBA;
  }

  .card-darkblue {
    background-color: #20358A;
  }

  .card-darkpurple {
    background-color: rgb(19, 9, 160);
  }
</style>
@endsection
