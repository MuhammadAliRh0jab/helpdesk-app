@extends('layouts.app')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <!-- Dynamic Page Header -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Daftar Tiket</h3>
            </div>
            <div class="block-content">
                @if ($canCreateTicket)
                    <div class="mb-3">
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">Buat Tiket Baru</a>
                    </div>
                @endif

                @if (auth()->user()->role_id == 2)
                    <!-- Tabs Nav -->
                    <ul class="custom-tabs nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="my-tickets-tab" data-bs-toggle="tab" data-bs-target="#my-tickets" type="button" role="tab" aria-controls="my-tickets" aria-selected="true">Tiket Saya</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="managed-tickets-tab" data-bs-toggle="tab" data-bs-target="#managed-tickets" type="button" role="tab" aria-controls="managed-tickets" aria-selected="false">Tiket yang Ditangani</button>
                        </li>
                    </ul>
                    <!-- END Tabs Nav -->

                    <!-- Tabs Content -->
                    <div class="tab-content">
                        <!-- My Tickets -->
                        <div class="tab-pane active" id="my-tickets" role="tabpanel" aria-labelledby="my-tickets-tab">
                            <div class="block-content block-content-full">
                                <!-- Search and Filter -->
                                <div class="search-filter-container mb-4">
                                    <form action="{{ route('tickets.index') }}" method="GET" class="search-filter-form">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control search-input" placeholder="Cari tiket..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary search-button">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="filter-group">
                                            <select name="status_filter" class="form-control filter-select" onchange="this.form.submit()">
                                                <option value="">Semua Status</option>
                                                <option value="0" {{ request('status_filter') == '0' ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Ditugaskan</option>
                                                <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="tab" value="my-tickets">
                                    </form>
                                </div>

                                <!-- Ticket Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-vcenter">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 5%;">#</th>
                                                <th style="width: 15%;">Tanggal</th>
                                                <th style="width: 10%;">Kode</th>
                                                <th style="width: 20%;">Judul</th>
                                                <th style="width: 10%;">Status</th>
                                                <th class="d-none d-md-table-cell" style="width: 10%;">Layanan</th>
                                                <th class="d-none d-md-table-cell" style="width: 10%;">Unit</th>
                                                <th class="d-none d-md-table-cell" style="width: 15%;">PIC</th>
                                                <th style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($myTickets as $ticket)
                                                <tr class="text-center align-middle">
                                                    <td class="p-1">{{ ($myTickets->currentPage() - 1) * $myTickets->perPage() + $loop->iteration }}</td>
                                                    <td class="p-1">{{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y') }}<br>{{ $ticket->created_at->timezone('Asia/Jakarta')->format('H.i') }}</td>
                                                    <td class="p-1">{{ $ticket->ticket_code }}</td>
                                                    <td class="p-1 text-start text-truncate" title="{{ $ticket->title }}">{{ $ticket->title }}</td>
                                                    <td class="p-1">
                                                        <span class="ticket-status badge 
                                                            {{ $ticket->status == 0 ? 'badge-warning' : ($ticket->status == 1 ? 'badge-info' : 'badge-success') }}">
                                                            {{ $ticket->status == 0 ? 'Pending' : ($ticket->status == 1 ? 'Ditugaskan' : 'Selesai') }}
                                                        </span>
                                                    </td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate">
                                                        @foreach($ticket->pics as $pic)
                                                            <div title="{{ $pic->user->username }} ({{ $pic->pic_desc }}) {{ $pic->pivot->pic_stats === 'inactive' ? '[Nonaktif]' : '' }}">
                                                                {{ Str::limit($pic->user->username, 10) }}
                                                                @if($pic->pivot->pic_stats === 'inactive')
                                                                    <span class="text-danger">[Nonaktif]</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="p-1 ticket-actions">
                                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                                                            <i class="fas fa-eye me-1"></i> Detail
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                                                            <i class="fas fa-comments me-1"></i> Pesan
                                                        </button>
                                                        @if($ticket->status == 0)
                                                            <button type="button" class="btn btn-outline-info btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $ticket->id }}" title="Tugaskan PIC">
                                                                <i class="fas fa-user-plus me-1"></i> Tugaskan
                                                            </button>
                                                            <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#transferModal-{{ $ticket->id }}" title="Alihkan Unit">
                                                                <i class="fas fa-exchange-alt me-1"></i> Alihkan
                                                            </button>
                                                        @endif
                                                        @if($ticket->pics->where('pivot.pic_stats', 'active')->count() > 0)
                                                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#removePicModal-{{ $ticket->id }}" title="Hapus PIC">
                                                                <i class="fas fa-user-minus me-1"></i> Hapus PIC
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @include('theme::tickets.partials.detail-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.chat-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.assign-modal', ['ticket' => $ticket, 'pics' => $pics])
                                                @include('theme::tickets.partials.transfer-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.remove-pic-modal', ['ticket' => $ticket])
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="p-2 text-center">Tidak ada tiket yang ditemukan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if ($myTickets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    <div class="d-flex justify-content-center flex-wrap mt-4">
                                        {{ $myTickets->appends(['status_filter' => request('status_filter'), 'search' => request('search'), 'tab' => 'my-tickets'])->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- END My Tickets -->

                        <!-- Managed Tickets -->
                        <div class="tab-pane" id="managed-tickets" role="tabpanel" aria-labelledby="managed-tickets-tab">
                            <div class="block-content block-content-full">
                                <!-- Search and Filter -->
                                <div class="search-filter-container mb-4">
                                    <form action="{{ route('tickets.index') }}" method="GET" class="search-filter-form">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control search-input" placeholder="Cari tiket..." value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary search-button">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        <div class="filter-group">
                                            <select name="status_filter" class="form-control filter-select" onchange="this.form.submit()">
                                                <option value="">Semua Status</option>
                                                <option value="0" {{ request('status_filter') == '0' ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Ditugaskan</option>
                                                <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="tab" value="managed-tickets">
                                    </form>
                                </div>

                                <!-- Ticket Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-vcenter">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 5%;">#</th>
                                                <th style="width: 15%;">Tanggal</th>
                                                <th style="width: 10%;">Kode</th>
                                                <th style="width: 20%;">Judul</th>
                                                <th style="width: 10%;">Status</th>
                                                <th class="d-none d-md-table-cell" style="width: 10%;">Layanan</th>
                                                <th class="d-none d-md-table-cell" style="width: 10%;">Unit</th>
                                                <th class="d-none d-md-table-cell" style="width: 15%;">PIC</th>
                                                <th style="width: 15%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($managedTickets as $ticket)
                                                <tr class="text-center align-middle">
                                                    <td class="p-1">{{ ($managedTickets->currentPage() - 1) * $managedTickets->perPage() + $loop->iteration }}</td>
                                                    <td class="p-1">{{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y') }}<br>{{ $ticket->created_at->timezone('Asia/Jakarta')->format('H.i') }}</td>
                                                    <td class="p-1">{{ $ticket->ticket_code }}</td>
                                                    <td class="p-1 text-start text-truncate" title="{{ $ticket->title }}">{{ $ticket->title }}</td>
                                                    <td class="p-1">
                                                        <span class="ticket-status badge 
                                                            {{ $ticket->status == 0 ? 'badge-warning' : ($ticket->status == 1 ? 'badge-info' : 'badge-success') }}">
                                                            {{ $ticket->status == 0 ? 'Pending' : ($ticket->status == 1 ? 'Ditugaskan' : 'Selesai') }}
                                                        </span>
                                                    </td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                                                    <td class="p-1 d-none d-md-table-cell text-truncate">
                                                        @foreach($ticket->pics as $pic)
                                                            <div title="{{ $pic->user->username }} ({{ $pic->pic_desc }}) {{ $pic->pivot->pic_stats === 'inactive' ? '[Nonaktif]' : '' }}">
                                                                {{ Str::limit($pic->user->username, 10) }}
                                                                @if($pic->pivot->pic_stats === 'inactive')
                                                                    <span class="text-danger">[Nonaktif]</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td class="p-1 ticket-actions">
                                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                                                            <i class="fas fa-eye me-1"></i> Detail
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                                                            <i class="fas fa-comments me-1"></i> Pesan
                                                        </button>
                                                        @if($ticket->status == 0)
                                                            <button type="button" class="btn btn-outline-info btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#assignModal-{{ $ticket->id }}" title="Tugaskan PIC">
                                                                <i class="fas fa-user-plus me-1"></i> Tugaskan
                                                            </button>
                                                            <button type="button" class="btn btn-outline-warning btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#transferModal-{{ $ticket->id }}" title="Alihkan Unit">
                                                                <i class="fas fa-exchange-alt me-1"></i> Alihkan
                                                            </button>
                                                        @endif
                                                        @if($ticket->pics->where('pivot.pic_stats', 'active')->count() > 0)
                                                            <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#removePicModal-{{ $ticket->id }}" title="Hapus PIC">
                                                                <i class="fas fa-user-minus me-1"></i> Hapus PIC
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @include('theme::tickets.partials.detail-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.chat-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.assign-modal', ['ticket' => $ticket, 'pics' => $pics])
                                                @include('theme::tickets.partials.transfer-modal', ['ticket' => $ticket])
                                                @include('theme::tickets.partials.remove-pic-modal', ['ticket' => $ticket])
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="p-2 text-center">Tidak ada tiket yang ditemukan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if ($managedTickets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    <div class="d-flex justify-content-center flex-wrap mt-4">
                                        {{ $managedTickets->appends(['status_filter' => request('status_filter'), 'search' => request('search'), 'tab' => 'managed-tickets'])->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- END Managed Tickets -->
                    </div>
                    <!-- END Tabs Content -->
                @else
                    <!-- Single Tab for Role 4 (Warga) and Role 3 (Pegawai) -->
                    <div class="block-content block-content-full">
                        <!-- Search and Filter -->
                        <div class="search-filter-container mb-4">
                            <form action="{{ route('tickets.index') }}" method="GET" class="search-filter-form">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control search-input" placeholder="Cari tiket..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary search-button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="filter-group">
                                    <select name="status_filter" class="form-control filter-select" onchange="this.form.submit()">
                                        <option value="">Semua Status</option>
                                        <option value="0" {{ request('status_filter') == '0' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ request('status_filter') == '1' ? 'selected' : '' }}>Ditugaskan</option>
                                        <option value="2" {{ request('status_filter') == '2' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <!-- Ticket Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-vcenter">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th style="width: 15%;">Tanggal</th>
                                        <th style="width: 10%;">Kode</th>
                                        <th style="width: 20%;">Judul</th>
                                        <th style="width: 10%;">Status</th>
                                        <th class="d-none d-md-table-cell" style="width: 15%;">Layanan</th>
                                        <th class="d-none d-md-table-cell" style="width: 15%;">Unit</th>
                                        <th style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                        <tr class="text-center align-middle">
                                            <td class="p-1">{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
                                            <td class="p-1">{{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y') }}<br>{{ $ticket->created_at->timezone('Asia/Jakarta')->format('H.i') }}</td>
                                            <td class="p-1">{{ $ticket->ticket_code }}</td>
                                            <td class="p-1 text-start text-truncate" title="{{ $ticket->title }}">{{ $ticket->title }}</td>
                                            <td class="p-1">
                                                <span class="ticket-status badge 
                                                    {{ $ticket->status == 0 ? 'badge-warning' : ($ticket->status == 1 ? 'badge-info' : 'badge-success') }}">
                                                    {{ $ticket->status == 0 ? 'Pending' : ($ticket->status == 1 ? 'Ditugaskan' : 'Selesai') }}
                                                </span>
                                                @if(auth()->user()->role_id == 3 && $ticket->status != 2)
                                                    <div class="ticket-status-update mt-1">
                                                        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                                <option value="0" {{ $ticket->status == 0 ? 'selected' : '' }}>Pending</option>
                                                                <option value="1" {{ $ticket->status == 1 ? 'selected' : '' }}>Ditugaskan</option>
                                                                <option value="2">Selesai</option>
                                                            </select>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                            <td class="p-1 d-none d-md-table-cell text-truncate" title="{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                                            <td class="p-1 ticket-actions">
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm w-100 mb-1" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                                                    <i class="fas fa-comments me-1"></i> Pesan
                                                </button>
                                            </td>
                                        </tr>
                                        @include('theme::tickets.partials.detail-modal', ['ticket' => $ticket])
                                        @include('theme::tickets.partials.chat-modal', ['ticket' => $ticket])
                                    @empty
                                        <tr>
                                            <td colspan="8" class="p-2 text-center">Tidak ada tiket yang ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center flex-wrap mt-4">
                                {{ $tickets->appends(['status_filter' => request('status_filter'), 'search' => request('search')])->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        <!-- END Dynamic Page Header -->
    </div>
    <!-- END Page Content -->
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle dynamic service loading for transfer modal
            document.querySelectorAll('select[name="unit_id"]').forEach(function (unitSelect) {
                unitSelect.addEventListener('change', function () {
                    const unitId = this.value;
                    const serviceSelect = this.closest('form').querySelector('select[name="service_id"]');
                    if (!unitId) {
                        serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                        return;
                    }

                    fetch(`/get-services/${unitId}`)
                        .then(response => response.json())
                        .then(data => {
                            serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';
                            data.forEach(service => {
                                const option = document.createElement('option');
                                option.value = service.id;
                                option.textContent = service.svc_name;
                                serviceSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error fetching services:', error));
                });
            });

            // Handle AJAX form submission for chat
            document.querySelectorAll('.chat-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const modal = this.closest('.modal');
                    const chatMessages = modal.querySelector('.chat-messages');

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const messageDiv = document.createElement('div');
                            messageDiv.className = `mb-2 ${data.user.id == data.auth_user_id ? 'text-right' : 'text-left'}`;
                            messageDiv.innerHTML = `
                                <strong>${data.user.username}:</strong>
                                <p>${formData.get('message')}</p>
                                <small>${new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' })}</small>
                            `;
                            chatMessages.appendChild(messageDiv);
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                            form.querySelector('textarea').value = '';
                            form.querySelector('input[type="file"]').value = '';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengirim pesan.');
                    });
                });
            });

            // Ensure correct tab is active after page load
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab === 'managed-tickets') {
                document.querySelector('#my-tickets-tab').classList.remove('active');
                document.querySelector('#my-tickets').classList.remove('active', 'show');
                document.querySelector('#managed-tickets-tab').classList.add('active');
                document.querySelector('#managed-tickets').classList.add('active', 'show');
            }
        });
    </script>
@endsection