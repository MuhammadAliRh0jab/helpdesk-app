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
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
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
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr class="text-center">
                                    <td class="p-2 text-dark text-center">{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}</td>
                                    <td class="p-2 text-dark">{{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H.i') }}</td>
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
                                                    <select name="unit_id" id="unit_id-{{ $ticket->id }}" class="form-select mb-2" required>
                                                        <option value="">Pilih Unit</option>
                                                        @foreach (\App\Models\Unit::all() as $unit)
                                                            @if ($unit->id != $ticket->unit_id)
                                                                <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <select name="service_id" id="service_id-{{ $ticket->id }}" class="form-select mb-2" required>
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
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                                                <i class="fas fa-comments"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

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
                                <div class="modal fade bg-dark" id="chatModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="chatModalLabel-{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true">
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
            </div>
        </div>
    </main>
</div>

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @forelse($tickets as $ticket)
        const customBtn{{ $ticket->id }} = document.getElementById('custom-button-{{ $ticket->id }}');
        const fileInput{{ $ticket->id }} = document.getElementById('images-{{ $ticket->id }}');
        const fileName{{ $ticket->id }} = document.getElementById('file-name-{{ $ticket->id }}');

        if (customBtn{{ $ticket->id }} && fileInput{{ $ticket->id }} && fileName{{ $ticket->id }}) {
            customBtn{{ $ticket->id }}.addEventListener('click', function() {
                fileInput{{ $ticket->id }}.click();
            });

            fileInput{{ $ticket->id }}.addEventListener('change', function() {
                if (fileInput{{ $ticket->id }}.files.length > 0) {
                    if (fileInput{{ $ticket->id }}.files.length === 1) {
                        fileName{{ $ticket->id }}.textContent = fileInput{{ $ticket->id }}.files[0].name;
                    } else {
                        fileName{{ $ticket->id }}.textContent = fileInput{{ $ticket->id }}.files.length + ' file dipilih';
                    }
                } else {
                    fileName{{ $ticket->id }}.textContent = 'Tidak ada file dipilih';
                }
            });
        }

        // Initialize map when modal is shown
        const modal{{ $ticket->id }} = document.getElementById('detailModal-{{ $ticket->id }}');
        if (modal{{ $ticket->id }}) {
            modal{{ $ticket->id }}.addEventListener('shown.bs.modal', function () {
                @if ($ticket->latitude && $ticket->longitude)
                    const map = L.map('map-{{ $ticket->id }}').setView([{{ $ticket->latitude }}, {{ $ticket->longitude }}], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Set the default icon path
                    delete L.Icon.Default.prototype._getIconUrl;
                    L.Icon.Default.mergeOptions({
                        iconRetinaUrl: '{{ asset('assets/leaflet/images/marker-icon-2x.png') }}',
                        iconUrl: '{{ asset('assets/leaflet/images/marker-icon.png') }}',
                        shadowUrl: '{{ asset('assets/leaflet/images/marker-shadow.png') }}'
                    });

                    const marker = L.marker([{{ $ticket->latitude }}, {{ $ticket->longitude }}]).addTo(map);
                @endif
            });
        }
    @empty
    @endforelse
});
</script>
@endsection