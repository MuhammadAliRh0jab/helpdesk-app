@extends('layouts.app')

@section('title', 'Daftar Aduan')

@section('content')
@if (auth()->user()->role_id == 3)
    @php
        $isPicActive = \App\Models\Pic::where('user_id', auth()->user()->id)
            ->where('pic_stats', 'active')
            ->exists();
    @endphp
    @if (!$isPicActive)
        <div class="alert alert-warning p-4 mb-4 rounded">
            Anda belum ditugaskan sebagai PIC. Anda hanya dapat membuat atau melihat aduan yang Anda buat.
        </div>
    @endif
@endif

<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                <div class="flex-grow-1">
                    <h1 class="h3 fw-bold mb-1">Detail Aduan</h1>
                    <h2 class="fs-base lh-base fw-medium text-muted mb-0">Aduan yang telah dikirimkan</h2>
                </div>
                <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-alt">
                        <li class="breadcrumb-item">
                            <a class="link-fx" href="javascript:void(0)">Aduan</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">List</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="content">
            <div class="block block-rounded">
                <div class="block-content block-content-full overflow-x-auto">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" id="searchInput" class="form-control" placeholder="Cari berdasarkan kode atau judul..." value="{{ request('search') }}">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <select name="per_page" id="perPageSelect" class="form-select w-auto d-inline-block" style="width: auto;">
                                <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5 per halaman</option>
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-vcenter" id="ticketsTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 80px;">#</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="d-none d-sm-table-cell">Kode Tiket</th>
                                    <th class="d-none d-sm-table-cell">Judul</th>
                                    <th class="d-none d-sm-table-cell">Status</th>
                                    <th class="d-none d-sm-table-cell">Layanan</th>
                                    @if (auth()->user()->role_id == 2)
                                        <th class="d-none d-sm-table-cell">Tugaskan PIC</th>
                                        <th class="d-none d-sm-table-cell">Alihkan Unit</th>
                                    @endif
                                    <th class="d-none d-sm-table-cell">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ticketsBody">
                                @forelse($tickets as $ticket)
                                    <tr class="text-center">
                                        <td class="p-2 text-dark text-center">{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
                                        <td class="p-2 text-dark">{{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H:i') }}</td>
                                        <td class="p-2 text-dark">{{ $ticket->ticket_code }}</td>
                                        <td class="p-2 text-dark">{{ $ticket->title }}</td>
                                        <td class="p-2 text-dark">
                                            @if($ticket->status == 0)
                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>
                                            @elseif($ticket->status == 1)
                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>
                                            @else
                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                                            @endif
                                        </td>
                                        <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                        @if (auth()->user()->role_id == 2)
                                            <td class="p-2">
                                                @if ($ticket->status != 2)
                                                    @if ($pics->isNotEmpty())
                                                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="mb-2">
                                                            @csrf
                                                            <select name="pic_id" class="form-select mb-2">
                                                                <option value="">Pilih PIC</option>
                                                                @foreach ($pics as $pic)
                                                                    <option value="{{ $pic->id }}">{{ $pic->username }} ({{ $pic->pic_desc }})</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="submit" class="btn btn-success btn-sm" title="Tugaskan PIC"><i class="far fa-user"></i></button>
                                                        </form>
                                                        @php
                                                            $activePics = DB::table('ticket_pic')
                                                                ->join('pics', 'ticket_pic.pic_id', '=', 'pics.id')
                                                                ->join('users', 'pics.user_id', '=', 'users.id')
                                                                ->where('ticket_pic.ticket_id', $ticket->id)
                                                                ->where('ticket_pic.pic_stats', 'active')
                                                                ->select('users.id as user_id', 'users.username', 'pics.id as pic_id')
                                                                ->get();
                                                        @endphp
                                                        @if($activePics->isNotEmpty())
                                                            <ul class="list-group list-group-flush text-dark">
                                                                @foreach($activePics as $pic)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center p-1">
                                                                        <p style="font-weight: 600; font-size: 14px; color: #333; background: #f1f3f5; padding: 4px 8px; border-radius: 4px; display: inline-block; margin: 0;">{{ $pic->username }}</p>
                                                                        <form action="{{ route('tickets.removePic', $ticket) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="pic_id" value="{{ $pic->pic_id }}">
                                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus PIC">
                                                                                <i class="fa fa-fw fa-trash-can"></i>
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <span class="text-muted">Belum ada PIC ditugaskan.</span>
                                                        @endif
                                                    @else
                                                        <span class="text-danger">Tidak ada PIC tersedia untuk unit ini.</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Tiket sudah resolved</span>
                                                @endif
                                            </td>
                                            <td class="p-2">
                                                @if ($ticket->status == 0)
                                                    <form action="{{ route('tickets.transfer', $ticket) }}" method="POST" id="transferForm-{{ $ticket->id }}" class="transfer-form">
                                                        @csrf
                                                        <select name="unit_id" id="unit_id-{{ $ticket->id }}" class="form-select mb-2 unit-select" required>
                                                            <option value="">Pilih Unit</option>
                                                            @foreach (\App\Models\Unit::all() as $unit)
                                                                @if ($unit->id != $ticket->unit_id)
                                                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <select name="service_id" id="service_id-{{ $ticket->id }}" class="form-select mb-2 service-select" required>
                                                            <option value="">Pilih Layanan</option>
                                                        </select>
                                                        <button type="submit" class="btn btn-warning btn-sm">Alihkan</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">Tidak dapat dialihkan</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="action-button p-2 text-center">
                                            <div class="mb-2">
                                                @if (isset($ticket->id))
                                                    <button type="button" class="btn btn-primary btn-sm detail-btn" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">Detail tidak tersedia</span>
                                                @endif
                                            </div>
                                            <div>
                                                @if (isset($ticket->id))
                                                    <button type="button" class="btn btn-success btn-sm chat-btn" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                                                        <i class="fas fa-comments"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">Chat tidak tersedia</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
    
                                    @if (isset($ticket->id))
                                        <!-- Modal Detail -->
                                        <div class="modal fade bg-dark" id="detailModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="detailModalLabel-{{ $ticket->id }}">Detail Tiket: {{ $ticket->ticket_code }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Waktu Dibuat:</strong> {{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H:i') }}</p>
                                                        <p><strong>Kode Tiket:</strong> {{ $ticket->ticket_code }}</p>
                                                        <p><strong>Judul:</strong> {{ $ticket->title }}</p>
                                                        <p><strong>Status:</strong>
                                                            @if($ticket->status == 0)
                                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>
                                                            @elseif($ticket->status == 1)
                                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>
                                                            @else
                                                                <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                                                            @endif
                                                        </p>
                                                        <p><strong>Layanan:</strong> {{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</p>
                                                        <p><strong>Deskripsi:</strong> {{ $ticket->description }}</p>
                                                        <p><strong>Unit Asal:</strong> {{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</p>
                                                        <p><strong>Unit Saat Ini:</strong> {{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</p>
                                                        <div class="mt-3">
                                                            <strong>Lokasi Aduan:</strong>
                                                            @if ($ticket->latitude && $ticket->longitude)
                                                                <div class="d-flex flex-column gap-2 mt-2">
                                                                    <div class="d-flex gap-3">
                                                                        <p class="mb-0"><strong>Latitude:</strong> {{ $ticket->latitude }}</p>
                                                                        <p class="mb-0"><strong>Longitude:</strong> {{ $ticket->longitude }}</p>
                                                                    </div>
                                                                    <div id="map-{{ $ticket->id }}" style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                                </div>
                                                            @else
                                                                <p class="text-muted">Lokasi tidak tersedia.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Modal untuk Percakapan -->
                                        <div class="modal fade" id="chatModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="chatModalLabel-{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                                    <div class="modal-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                                                        <h5 class="modal-title d-flex align-items-center gap-2 m-0" id="chatModalLabel-{{ $ticket->id }}">
                                                            <i class="fas fa-ticket-alt" style="color: #2563eb;"></i>
                                                            <span style="font-weight: 600; font-size: 1rem; color: #1f2937;">{{ $ticket->ticket_code }}</span>
                                                            @if($ticket->status == 0)
                                                                <span class="badge bg-warning text-dark" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Pending</span>
                                                            @elseif($ticket->status == 1)
                                                                <span class="badge bg-info text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Ditugaskan</span>
                                                            @else
                                                                <span class="badge bg-success text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Selesai</span>
                                                            @endif
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-0" style="display: flex; flex-direction: column; max-height: 70vh;">
                                                        <div class="chat-container" id="chat-container-{{ $ticket->id }}" style="flex: 1; overflow-y: auto; padding: 1.25rem; background-color: #f9fafb;">
                                                            @forelse($ticket->responses as $response)
                                                                @php
                                                                    $isSender = $response->user_id == auth()->user()->id;
                                                                @endphp
                                                                <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: {{ $isSender ? 'flex-end' : 'flex-start' }};">
                                                                    <div class="message-info d-flex align-items-center gap-2 mb-1">
                                                                        @if (!$isSender)
                                                                            <div class="avatar" style="width: 28px; height: 28px; border-radius: 50%; background-color: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                                                                {{ substr($response->user->username, 0, 1) }}
                                                                            </div>
                                                                        @endif
                                                                        <span class="message-sender" style="font-weight: 600; font-size: 0.85rem; color: #374151;">
                                                                            @if ($response->user->role_id == 2)
                                                                                Sistem (Operator)
                                                                            @else
                                                                                {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
                                                                            @endif
                                                                        </span>
                                                                    </div>
                                                                    @if ($response->ticket_id_quote)
                                                                        <div class="message-quote" style="font-style: italic; {{ $isSender ? 'color: rgba(255, 255, 255, 0.8);' : 'color: #6b7280;' }} font-size: 0.8rem; margin-bottom: 6px; padding-left: 8px; border-left: 2px solid {{ $isSender ? 'rgba(255, 255, 255, 0.5)' : '#d1d5db' }};">
                                                                            "{{ $response->quotedResponse->message }}"
                                                                        </div>
                                                                    @endif
                                                                    <div class="message-box {{ $isSender ? 'sent' : 'received' }}" style="max-width: 80%; padding: 12px 16px; border-radius: 16px; margin-bottom: 0.5rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                                                        @forelse($response->uploads as $upload)
                                                                            <div class="message-attachment mb-2">
                                                                                <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                                                    <img src="{{ asset('storage/' . $upload->filename_path) }}" alt="{{ $upload->filename_ori }}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; {{ !$isSender ? 'border: 1px solid #e5e7eb;' : '' }}">
                                                                                </a>
                                                                            </div>
                                                                        @empty
                                                                        @endforelse
                                                                        <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">{{ $response->message }}</p>
                                                                        <span class="message-time" style="font-size: 0.7rem; {{ $isSender ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;' }} display: block; text-align: right; margin-top: 4px;">
                                                                            {{ $response->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                                            @endforelse
                                                        </div>
    
                                                        @php
                                                            $hasPegawaiResponse = $ticket->responses()
                                                                ->where('user_id', '!=', auth()->user()->id)
                                                                ->whereHas('user', function ($query) {
                                                                    $query->where('role_id', 3);
                                                                })
                                                                ->exists();
    
                                                            $pengaduMessagesSinceLastPegawai = $ticket->responses()->where('user_id', auth()->user()->id)->count();
                                                            if ($hasPegawaiResponse) {
                                                                $lastPegawaiResponse = $ticket->responses()
                                                                    ->where('user_id', '!=', auth()->user()->id)
                                                                    ->whereHas('user', function ($query) {
                                                                        $query->where('role_id', 3);
                                                                    })
                                                                    ->latest()
                                                                    ->first();
                                                                $pengaduMessagesSinceLastPegawai = $ticket->responses()
                                                                    ->where('user_id', auth()->user()->id)
                                                                    ->where('created_at', '>', $lastPegawaiResponse->created_at)
                                                                    ->count();
                                                            }
                                                        @endphp
    
                                                        @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $pengaduMessagesSinceLastPegawai < 10)
                                                            <div class="reply-container" style="background-color: white; border-top: 1px solid #e5e7eb; padding: 1rem;">
                                                                <form id="reply-form-{{ $ticket->id }}" action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="reply-form">
                                                                    @csrf
                                                                    <div class="reply-input-row d-flex gap-2 mb-2">
                                                                        <textarea class="form-control" name="message" placeholder="Ketik pesan Anda di sini..." required style="border-radius: 24px; border-color: #d1d5db; padding: 12px 16px; font-size: 0.9rem; resize: none;"></textarea>
                                                                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;">
                                                                            <i class="fas fa-paper-plane"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="attachment-row d-flex align-items-center gap-2">
                                                                        <button class="btn btn-outline-primary" type="button" id="custom-button-{{ $ticket->id }}" style="border-radius: 20px; padding: 6px 14px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                                            <i class="fas fa-paperclip"></i>
                                                                            <span>Lampirkan File</span>
                                                                        </button>
                                                                        <span id="file-name-{{ $ticket->id }}" class="text-muted" style="font-size: 0.85rem; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tidak ada file dipilih</span>
                                                                        <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role_id == 2 ? 10 : 8 }}" class="p-2 text-dark text-center">
                                            Tidak ada aduan yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4" id="paginationLinks">
                        {{ $tickets->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Pass user data to JavaScript -->
<script>
    window.app = {
        user: {
            id: {{ auth()->user()->id }},
            email: "{{ auth()->user()->email }}",
            role_id: {{ auth()->user()->role_id }}
        },
        messageLimit: {{ \App\Models\Setting::where('key', 'pengadu_message_limit')->value('value') ?? 10 }}
    };
</script>

<style>
/* Custom styling untuk chat modal */
.modal-content {
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Styling untuk tampilan pesan */
.chat-container .message-wrapper {
    margin-bottom: 16px;
}

.chat-container .message-box.sent {
    background-color: #2563eb;
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.chat-container .message-box.received {
    background-color: white;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

/* Styling untuk input area */
.reply-container textarea:focus {
    box-shadow: none;
    border-color: #2563eb;
}

/* Custom file input */
.attachment-row .btn-outline-primary {
    color: #2563eb;
    border-color: #2563eb;
}

.attachment-row .btn-outline-primary:hover {
    background-color: #dbeafe;
    color: #2563eb;
}

/* Styling untuk avatars */
.avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Styling untuk guest ticket badge */
.bg-secondary-light {
    background-color: #e5e7eb;
}

.text-secondary {
    color: #6b7280;
}

/* Ensure pagination style matches the image */
.pagination .page-item .page-link {
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    margin: 0 2px;
}

.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-item:first-child .page-link {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.pagination .page-item:last-child .page-link {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const perPageSelect = document.getElementById('perPageSelect');
    const ticketsBody = document.getElementById('ticketsBody');
    const paginationLinks = document.getElementById('paginationLinks');
    let debounceTimeout;

    function debounce(func, delay) {
        return function(...args) {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function loadTickets(page = 1) {
        const search = searchInput.value;
        const perPage = perPageSelect.value;
        const statusFilter = '{{ request('status_filter') }}';

        fetch(`/tickets?page=${page}&search=${encodeURIComponent(search)}&status_filter=${statusFilter}&per_page=${perPage}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            ticketsBody.innerHTML = '';
            if (data.tickets.data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="${window.app.role_id == 2 ? 10 : 8}" class="p-2 text-dark text-center">
                        Tidak ada aduan yang ditemukan.
                    </td>
                `;
                ticketsBody.appendChild(row);
            } else {
                data.tickets.data.forEach((ticket, index) => {
                    const row = document.createElement('tr');
                    row.className = 'text-center';
                    row.innerHTML = `
                        <td class="p-2 text-dark text-center">${(data.tickets.current_page - 1) * data.tickets.per_page + index + 1}</td>
                        <td class="p-2 text-dark">${new Date(ticket.created_at).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</td>
                        <td class="p-2 text-dark">${ticket.ticket_code}</td>
                        <td class="p-2 text-dark">${ticket.title}</td>
                        <td class="p-2 text-dark">
                            ${ticket.user_id ? 
                                (ticket.user ? ticket.user.username : 'Unknown') :
                                (ticket.guest_name ? ticket.guest_name : 'Guest') + ' <span class="fs-xs fw-semibold d-inline-block py-1 px-2 rounded-pill bg-secondary-light text-secondary ms-1">Guest</span>'
                            }
                        </td>
                        <td class="p-2 text-dark">
                            ${ticket.status == 0 ? '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>' :
                              ticket.status == 1 ? '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>' :
                              '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>'}
                        </td>
                        <td class="p-2 text-dark">${ticket.service ? ticket.service.svc_name : 'Tidak ditentukan'}</td>
                        ${window.app.role_id == 2 ? `
                            <td class="p-2">
                                ${ticket.status != 2 ? `
                                    ${data.pics.length > 0 ? `
                                        <form action="/tickets/assign/${ticket.id}" method="POST" class="mb-2">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                            <select name="pic_id" class="form-select mb-2">
                                                <option value="">Pilih PIC</option>
                                                ${data.pics.map(pic => `<option value="${pic.id}">${pic.username} (${pic.pic_desc})</option>`).join('')}
                                            </select>
                                            <button type="submit" class="btn btn-success btn-sm" title="Tugaskan PIC"><i class="far fa-user"></i></button>
                                        </form>
                                        ${ticket.pics && ticket.pics.some(pic => pic.ticket_pic && pic.ticket_pic.pic_stats === 'active') ? `
                                            <ul class="list-group list-group-flush text-dark">
                                                ${ticket.pics.filter(pic => pic.ticket_pic && pic.ticket_pic.pic_stats === 'active').map(pic => `
                                                    <li class="list-group-item d-flex justify-content-between align-items-center p-1">
                                                        <p style="font-weight: 600; font-size: 14px; color: #333; background: #f1f3f5; padding: 4px 8px; border-radius: 4px; display: inline-block; margin: 0;">${pic.user.username}</p>
                                                        <form action="/tickets/removePic/${ticket.id}" method="POST" class="d-inline">
                                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                                            <input type="hidden" name="pic_id" value="${pic.ticket_pic.pic_id}">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus PIC">
                                                                <i class="fa fa-fw fa-trash-can"></i>
                                                            </button>
                                                        </form>
                                                    </li>
                                                `).join('')}
                                            </ul>
                                        ` : '<span class="text-muted">Belum ada PIC ditugaskan.</span>'}
                                    ` : '<span class="text-danger">Tidak ada PIC tersedia untuk unit ini.</span>'}
                                ` : '<span class="text-muted">Tiket sudah resolved</span>'}
                            </td>
                            <td class="p-2">
                                ${ticket.status == 0 ? `
                                    <form action="/tickets/transfer/${ticket.id}" method="POST" id="transferForm-${ticket.id}" class="transfer-form">
                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                        <select name="unit_id" id="unit_id-${ticket.id}" class="form-select mb-2 unit-select" required>
                                            <option value="">Pilih Unit</option>
                                            ${data.units.filter(unit => unit.id !== ticket.unit_id).map(unit => `<option value="${unit.id}">${unit.unit_name}</option>`).join('')}
                                        </select>
                                        <select name="service_id" id="service_id-${ticket.id}" class="form-select mb-2 service-select" required>
                                            <option value="">Pilih Layanan</option>
                                        </select>
                                        <button type="submit" class="btn btn-warning btn-sm">Alihkan</button>
                                    </form>
                                ` : '<span class="text-muted">Tidak dapat dialihkan</span>'}
                            </td>
                        ` : ''}
                        <td class="action-button p-2 text-center">
                            <div class="mb-2">
                                <button type="button" class="btn btn-primary btn-sm detail-btn" data-bs-toggle="modal" data-bs-target="#detailModal-${ticket.id}" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-success btn-sm chat-btn" data-bs-toggle="modal" data-bs-target="#chatModal-${ticket.id}" title="Pesan">
                                    <i class="fas fa-comments"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    ticketsBody.appendChild(row);

                    // Dynamically append modals for each ticket
                    const modalContainer = document.createElement('div');
                    modalContainer.innerHTML = `
                        <!-- Modal Detail -->
                        <div class="modal fade bg-dark" id="detailModal-${ticket.id}" tabindex="-1" aria-labelledby="detailModalLabel-${ticket.id}" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel-${ticket.id}">Detail Tiket: ${ticket.ticket_code}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Waktu Dibuat:</strong> ${new Date(ticket.created_at).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                        <p><strong>Kode Tiket:</strong> ${ticket.ticket_code}</p>
                                        <p><strong>Judul:</strong> ${ticket.title}</p>
                                        <p><strong>Pengadu:</strong> ${ticket.user_id ? (ticket.user ? ticket.user.username : 'Unknown') : (ticket.guest_name ? ticket.guest_name : 'Guest') + ' (Guest)'}</p>
                                        <p><strong>Status:</strong>
                                            ${ticket.status == 0 ? '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>' :
                                              ticket.status == 1 ? '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>' :
                                              '<span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>'}
                                        </p>
                                        <p><strong>Layanan:</strong> ${ticket.service ? ticket.service.svc_name : 'Tidak ditentukan'}</p>
                                        <p><strong>Deskripsi:</strong> ${ticket.description}</p>
                                        <p><strong>Unit Asal:</strong> ${ticket.original_unit_id ? data.units.find(unit => unit.id == ticket.original_unit_id)?.unit_name : (ticket.unit ? ticket.unit.unit_name : 'Tidak ditentukan')}</p>
                                        <p><strong>Unit Saat Ini:</strong> ${ticket.unit ? ticket.unit.unit_name : 'Tidak ditentukan'}</p>
                                        <div class="mt-3">
                                            <strong>Lokasi Aduan:</strong>
                                            ${ticket.latitude && ticket.longitude ? `
                                                <div class="d-flex flex-column gap-2 mt-2">
                                                    <div class="d-flex gap-3">
                                                        <p class="mb-0"><strong>Latitude:</strong> ${ticket.latitude}</p>
                                                        <p class="mb-0"><strong>Longitude:</strong> ${ticket.longitude}</p>
                                                    </div>
                                                    <div id="map-${ticket.id}" style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                                </div>
                                            ` : '<p class="text-muted">Lokasi tidak tersedia.</p>'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal untuk Percakapan -->
                        <div class="modal fade" id="chatModal-${ticket.id}" tabindex="-1" aria-labelledby="chatModalLabel-${ticket.id}" data-bs-backdrop="static" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content shadow-lg" style="border-radius: 12px; overflow: hidden;">
                                    <div class="modal-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                                        <h5 class="modal-title d-flex align-items-center gap-2 m-0" id="chatModalLabel-${ticket.id}">
                                            <i class="fas fa-ticket-alt" style="color: #2563eb;"></i>
                                            <span style="font-weight: 600; font-size: 1rem; color: #1f2937;">${ticket.ticket_code}</span>
                                            ${ticket.status == 0 ? '<span class="badge bg-warning text-dark" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Pending</span>' :
                                              ticket.status == 1 ? '<span class="badge bg-info text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Ditugaskan</span>' :
                                              '<span class="badge bg-success text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Selesai</span>'}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0" style="display: flex; flex-direction: column; max-height: 70vh;">
                                        <div class="chat-container" id="chat-container-${ticket.id}" style="flex: 1; overflow-y: auto; padding: 1.25rem; background-color: #f9fafb;">
                                            ${ticket.responses && ticket.responses.length > 0 ? ticket.responses.map(response => `
                                                <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: ${response.user_id == window.app.user.id ? 'flex-end' : 'flex-start'};">
                                                    <div class="message-info d-flex align-items-center gap-2 mb-1">
                                                        ${response.user_id != window.app.user.id ? `
                                                            <div class="avatar" style="width: 28px; height: 28px; border-radius: 50%; background-color: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                                                ${response.user ? response.user.username.charAt(0) : 'U'}
                                                            </div>
                                                        ` : ''}
                                                        <span class="message-sender" style="font-weight: 600; font-size: 0.85rem; color: #374151;">
                                                            ${response.user && response.user.role_id == 2 ? 'Sistem (Operator)' :
                                                              response.user ? `${response.user.username} (${response.user.role_id == 4 ? 'Pengadu' : 'PIC'})` : 'Unknown'}
                                                        </span>
                                                    </div>
                                                    ${response.ticket_id_quote ? `
                                                        <div class="message-quote" style="font-style: italic; ${response.user_id == window.app.user.id ? 'color: rgba(255, 255, 255, 0.8);' : 'color: #6b7280;'} font-size: 0.8rem; margin-bottom: 6px; padding-left: 8px; border-left: 2px solid ${response.user_id == window.app.user.id ? 'rgba(255, 255, 255, 0.5)' : '#d1d5db'};">
                                                            "${response.quotedResponse ? response.quotedResponse.message : 'Quoted message not found'}"
                                                        </div>
                                                    ` : ''}
                                                    <div class="message-box ${response.user_id == window.app.user.id ? 'sent' : 'received'}" style="max-width: 80%; padding: 12px 16px; border-radius: 16px; margin-bottom: 0.5rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                                        ${response.uploads && response.uploads.length > 0 ? response.uploads.map(upload => `
                                                            <div class="message-attachment mb-2">
                                                                <a href="/storage/${upload.filename_path}" target="_blank">
                                                                    <img src="/storage/${upload.filename_path}" alt="${upload.filename_ori}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; ${response.user_id != window.app.user.id ? 'border: 1px solid #e5e7eb;' : ''}">
                                                                </a>
                                                            </div>
                                                        `).join('') : ''}
                                                        <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">${response.message}</p>
                                                        <span class="message-time" style="font-size: 0.7rem; ${response.user_id == window.app.user.id ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;'} display: block; text-align: right; margin-top: 4px;">
                                                            ${new Date(response.created_at).toLocaleString('id-ID', { timeZone: 'Asia/Jakarta', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                                        </span>
                                                    </div>
                                                </div>
                                            `).join('') : '<p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>'}
                                        </div>
                                        ${ticket.can_reply ? `
                                            <div class="reply-container" style="background-color: white; border-top: 1px solid #e5e7eb; padding: 1rem;">
                                                <form id="reply-form-${ticket.id}" action="/tickets/reply/${ticket.id}" method="POST" enctype="multipart/form-data" class="reply-form">
                                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                                    <div class="reply-input-row d-flex gap-2 mb-2">
                                                        <textarea class="form-control" name="message" placeholder="Ketik pesan Anda di sini..." required style="border-radius: 24px; border-color: #d1d5db; padding: 12px 16px; font-size: 0.9rem; resize: none;"></textarea>
                                                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </div>
                                                    <div class="attachment-row d-flex align-items-center gap-2">
                                                        <button class="btn btn-outline-primary" type="button" id="custom-button-${ticket.id}" style="border-radius: 20px; padding: 6px 14px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                            <i class="fas fa-paperclip"></i>
                                                            <span>Lampirkan File</span>
                                                        </button>
                                                        <span id="file-name-${ticket.id}" class="text-muted" style="font-size: 0.85rem; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tidak ada file dipilih</span>
                                                        <input type="file" name="images[]" id="images-${ticket.id}" multiple class="form-control d-none">
                                                    </div>
                                                </form>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modalContainer);
                });
            }

            paginationLinks.innerHTML = data.pagination;
            attachTransferFormListeners();
            attachPaginationListeners();
            attachReplyFormListeners();

            // Reattach modal event listeners
            document.querySelectorAll('.detail-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    console.log('Detail button clicked for ticket:', btn.getAttribute('data-bs-target'));
                });
            });

            document.querySelectorAll('.chat-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    console.log('Chat button clicked for ticket:', btn.getAttribute('data-bs-target'));
                });
            });
        })
        .catch(error => {
            console.error('Error loading tickets:', error);
            ticketsBody.innerHTML = `<tr><td colspan="${window.app.role_id == 2 ? 10 : 8}" class="p-2 text-dark text-center">Terjadi kesalahan saat memuat data tiket. Silakan coba lagi.</td></tr>`;
            paginationLinks.innerHTML = '';
        });
    }

    function attachTransferFormListeners() {
        document.querySelectorAll('.transfer-form .unit-select').forEach(select => {
            select.addEventListener('change', function() {
                const form = this.closest('form');
                const serviceSelect = form.querySelector('.service-select');
                const unitId = this.value;

                serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';

                if (unitId) {
                    fetch(`/services?unit_id=${unitId}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(services => {
                        services.forEach(service => {
                            const option = document.createElement('option');
                            option.value = service.id;
                            option.textContent = service.svc_name;
                            serviceSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching services:', error));
                }
            });
        });
    }

    function attachPaginationListeners() {
        const paginationLinks = document.getElementById('paginationLinks');
        paginationLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (!this.classList.contains('disabled')) {
                    const page = this.getAttribute('data-page') || new URL(this.href).searchParams.get('page') || 1;
                    loadTickets(page);
                }
            });
        });
    }

    function attachReplyFormListeners() {
        document.querySelectorAll('.reply-form').forEach(form => {
            const ticketId = form.id.replace('reply-form-', '');
            const fileInput = document.getElementById(`images-${ticketId}`);
            const fileNameDisplay = document.getElementById(`file-name-${ticketId}`);
            const customButton = document.getElementById(`custom-button-${ticketId}`);

            customButton.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    const fileNames = Array.from(fileInput.files).map(file => file.name).join(', ');
                    fileNameDisplay.textContent = fileNames;
                } else {
                    fileNameDisplay.textContent = 'Tidak ada file dipilih';
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Reply submitted successfully:', data);
                        loadTickets(data.tickets.current_page);
                    } else {
                        console.error('Error submitting reply:', data.message);
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error submitting reply:', error);
                    alert('Terjadi kesalahan saat mengirim pesan.');
                });
            });
        });
    }

    searchInput.addEventListener('input', debounce(function() {
        loadTickets(1);
    }, 500));

    perPageSelect.addEventListener('change', function() {
        loadTickets(1);
    });

    attachPaginationListeners();
    attachTransferFormListeners();
    attachReplyFormListeners();
});
</script>
@endsection