@extends('layouts.app')

@section('title', 'Dashboard Warga')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h6><strong>Dashboard</strong></h6> 
        <hr><br>
        <h6 class="card-title- mb-2 fs-5">Selamat Datang, <strong>{{ Auth::user()->name }}</strong> &#128075;&#128522; !</h6>
        <p>Kami siap membantu Anda melaporkan aduan, mengelola tiket, dan mendapatkan solusi cepat untuk berbagai permasalahan.</p>
    </div>
</div>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div></div>
    </div>
</div>

<hr>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="flex-column align-items-center justify-content-center" style="margin-top: -20px;">
            <canvas id="ticketChart"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="row g-3">
            <!-- Cards Tiket -->
            <div class="col-md-6">
                <div class="card-custom card-blue">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-ticket"></i>
                        <div class="d-flex flex-column text-end">
                            <h6 class="card-title">Total Tiket</h6>
                            <p class="fs-2 text-white">{{ $ticketStats['completed'] + $ticketStats['pending'] + $ticketStats['assigned'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom card-darkblue">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-briefcase"></i>
                        <div class="d-flex flex-column text-end">
                            <h6 class="card-title">Tiket Ditugaskan</h6>
                            <p class="fs-2 text-white">{{ $ticketStats['assigned'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom card-purple">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-hourglass-half"></i>
                        <div class="d-flex flex-column text-end">
                            <h6 class="card-title">Tiket Pending</h6>
                            <p class="fs-2 text-white">{{ $ticketStats['pending'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom card-darkpurple">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-circle-check"></i>
                        <div class="d-flex flex-column text-end">
                            <h6 class="card-title">Tiket Selesai</h6>
                            <p class="fs-2 text-white">{{ $ticketStats['completed'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Daftar Aduan Anda</h5>
            </div>
            {{-- <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Tiket</th>
                            <th>Judul</th>
                            <th>Unit</th>
                            <th>Layanan</th>
                            <th>Status</th>
                            <th>Dibuat Pada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($tickets->count() > 0)
                            @foreach ($tickets as $index => $ticket)
                                <tr class="text-nowrap">
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $key + 1 }}</td>
                                    <td>{{ $ticket->ticket_code }}</td>
                                    <td>{{ $ticket->title }}</td>
                                    <td>{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                                    <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                    <td>
                                        @if($ticket->status == 0)
                                            <span class="badge bg-warning">Belum Direspon</span>
                                        @elseif($ticket->status == 1)
                                            <span class="badge bg-info">Direspon</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="alert alert-info m-0">
                                                Anda belum memiliki tiket. Silahkan buka halaman tiket dan klik Buat Aduan untuk membuat tiket.
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div> --}}
            <!-- Pagination dipindah keluar table -->
            {{-- <div class="d-flex justify-content-center mt-4">
                {{ $tickets->links() }}
            </div> --}}
            <livewire:aduan-table />
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const ticketStats = {
    assigned: {{ $ticketStats['assigned'] ?? 0 }},
    pending: {{ $ticketStats['pending'] ?? 0 }},
    completed: {{ $ticketStats['completed'] ?? 0 }}
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
                    color: '#11111',
                    boxWidth: 15,
                    padding: 40
                }
            }
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const ticketChartCtx = document.getElementById('ticketChart');
    if (ticketChartCtx) {
        new Chart(ticketChartCtx, {
            ...chartOptions,
            data: {
                labels: ['Ditugaskan', 'Pending', 'Selesai'],
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: [ticketStats.assigned, ticketStats.pending, ticketStats.completed],
                    backgroundColor: ['#20358A', '#6D5DBA', '#3429D5'],
                    borderWidth: 1,
                    radius: '80%'
                }]
            }
        });
    }
});
</script>

<style>
body {
    background-color: #f5f7ff;
    font-family: 'Segoe UI', sans-serif;
}

.sidebar {
    height: 100vh;
    background-color: #fff;
    border-right: 1px solid #ddd;
}

.sidebar .nav-link.active {
    background-color: #6c63ff;
    color: white !important;
    border-radius: 8px;
}

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

.table th {
    color: rgb(124, 124, 124);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
