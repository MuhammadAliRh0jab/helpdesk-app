@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">Aduan Ditugaskan</h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">Aduan yang ditugaskan harus segera diproses dan dibalas</h2>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="alert alert-success p-4 mb-4 mt-4 rounded">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger p-4 mb-4 mt-4 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="content">
            <div class="row">
                <div class="col-xl-12">
                    <div class="block block-rounded">
                        <div class="block-header block-header-default">
                            <!-- <h3 class="block-title">Riwayat Aduan</h3> -->
                        </div>
                        <div class="block-content">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr class="">
                                        <th>#</th>
                                        <th>Kode Tiket</th>
                                        <th>Judul</th>
                                        <th>Layanan</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tickets as $ticket)
                                    <tr class="">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ticket->ticket_code }}</td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                        <td>{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                                        <td>
                                            @if($ticket->status == 0) Pending
                                            @elseif($ticket->status == 1) Ditugaskan
                                            @else Resolved
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <!-- Tombol Detail -->
                                                <button type="button" class="btn btn-sm btn-primary detail-btn" data-bs-toggle="modal" data-bs-target="#detailModal{{ $ticket->id }}" title="Detail Aduan">
                                                    <i class="fas fa-eye me-1"></i>
                                                </button>
                                                <!-- Tombol Balas Pesan -->
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTicket{{ $ticket->id }}" title="Balas Pesan">
                                                    <i class="fas fa-reply me-1"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Percakapan Gaya Chat -->
                                    <div class="modal fade bg-dark" id="modalTicket{{ $ticket->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel{{ $ticket->id }}">Kode Tiket: {{ $ticket->ticket_code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body" style="padding: 0; display: flex; flex-direction: column">
                                                    <div class="chat-container px-4 py-3" id="chat-container-{{ $ticket->id }}" style="flex: 1; overflow-y: auto; background-color: #f9fafb;">
                                                        <div class="mb-4">
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
                                                                <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">{{ $response->message }}</p>
                                                                @foreach($response->uploads as $upload)
                                                                <div class="message-attachment mb-2">
                                                                    <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                                        <img src="{{ asset('storage/' . $upload->filename_path) }}" alt="{{ $upload->filename_ori }}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; {{ !$isSender ? 'border: 1px solid #e5e7eb;' : '' }}">
                                                                    </a>
                                                                </div>
                                                                @endforeach
                                                                <span class="message-time" style="font-size: 0.7rem; {{ $isSender ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;' }} display: block; text-align: right; margin-top: 4px;">
                                                                    {{ $response->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        @empty
                                                        <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                                        @endforelse
                                                    </div>

                                                    @if ($ticket->status != 2)
                                                    <div class="reply-container" style="background-color: white; border-top: 1px solid #e5e7eb; padding: 1rem;">
                                                        <form id="chat-form-{{ $ticket->id }}" action="{{ route('tickets.respond', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="reply-input-row d-flex gap-2 mb-2">
                                                                <textarea class="form-control" name="message" placeholder="Masukkan pesan..." required style="border-radius: 24px; border-color: #d1d5db; padding: 12px 16px; font-size: 0.9rem; resize: none;"></textarea>
                                                                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;" title="Kirim Pesan">
                                                                    <i class="fas fa-paper-plane"></i>
                                                                </button>
                                                            </div>
                                                            <div class="attachment-row d-flex align-items-center gap-2">
                                                                <button class="btn btn-outline-primary" type="button" id="custom-button-{{ $ticket->id }}" style="border-radius: 20px; padding: 6px 14px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                                    <i class="fas fa-paperclip"></i>
                                                                    <span>Pilih File</span>
                                                                </button>
                                                                <span id="file-name-{{ $ticket->id }}" class="text-muted" style="font-size: 0.85rem; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tidak ada file dipilih</span>
                                                                <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                                                @if ($ticket->status == 1)
                                                                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center resolve-ticket-btn" data-ticket-id="{{ $ticket->id }}" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;" title="Tandai Selesai">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </form>

                                                        @if ($ticket->status == 1)
                                                        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" id="resolve-form-{{ $ticket->id }}" class="d-none">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="2">
                                                        </form>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Detail -->
                                    <!-- Modal Detail -->
<div class="modal fade bg-dark" id="detailModal{{ $ticket->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel{{ $ticket->id }}">Detail Tiket: {{ $ticket->ticket_code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Waktu Dibuat:</strong> {{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H:i') }}</p>
                <p><strong>Kode Tiket:</strong> {{ $ticket->ticket_code }}</p>
                <p><strong>Judul:</strong> {{ $ticket->title }}</p>
                <p><strong>Layanan:</strong> {{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</p>
                <p><strong>Unit:</strong> {{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</p>
                <p><strong>Status:</strong>
                    @if($ticket->status == 0)
                        <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>
                    @elseif($ticket->status == 1)
                        <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>
                    @else
                        <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                    @endif
                </p>
                <p><strong>Deskripsi:</strong> {{ $ticket->description }}</p>
                <div class="mt-3">
                    <strong>Lampiran:</strong>
                    @if ($ticket->uploads->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach ($ticket->uploads as $attachment)
                                <div class="message-attachment">
                                    <a href="{{ asset('storage/' . $attachment->filename_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $attachment->filename_path) }}" alt="{{ $attachment->filename_ori }}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Tidak ada lampiran.</p>
                    @endif
                </div>
                <div class="mt-3">
                    <strong>Lokasi Aduan:</strong>
                    @if ($ticket->latitude && $ticket->longitude)
                        <div class="d-flex flex-column gap-2 mt-2">
                            <div class="d-flex gap-3">
                                <p class="mb-0"><strong>Latitude:</strong> {{ $ticket->latitude }}</p>
                                <p class="mb-0"><strong>Longitude:</strong> {{ $ticket->longitude }}</p>
                            </div>
                            <div id="mapDetail-{{ $ticket->id }}" style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                        </div>
                    @else
                        <p class="text-muted">Lokasi tidak tersedia.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Tidak ada aduan yang ditugaskan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .modal-content {
        position: relative;
        z-index: 1;
    }

    .modal-content>* {
        position: relative;
        z-index: 1;
    }

    @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

    .modal-content {
        font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    /* Custom styling for chat modal */
    .modal-content {
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* Styling for chat messages */
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

    /* Styling for input area */
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

    /* Styling for avatars */
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

    .swal2-container {
        z-index: 9999 !important;
    }

    /* Styling untuk tombol detail */
    .btn-sm.btn-primary.detail-btn {
        background-color: #4b5563;
        border-color: #4b5563;
    }

    .btn-sm.btn-primary.detail-btn:hover {
        background-color: #6b7280;
        border-color: #6b7280;
    }

    /* Pastikan modal detail memiliki z-index yang cukup */
    .modal.fade.bg-dark {
        z-index: 1055;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Initialize map for chat modal when shown
        const modal{{ $ticket->id }} = document.getElementById('modalTicket{{ $ticket->id }}');
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

        // Initialize map for detail modal when shown
        const detailModal{{ $ticket->id }} = document.getElementById('detailModal{{ $ticket->id }}');
        if (detailModal{{ $ticket->id }}) {
            detailModal{{ $ticket->id }}.addEventListener('shown.bs.modal', function () {
                @if ($ticket->latitude && $ticket->longitude)
                    const detailMap = L.map('mapDetail-{{ $ticket->id }}').setView([{{ $ticket->latitude }}, {{ $ticket->longitude }}], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(detailMap);

                    // Set the default icon path
                    delete L.Icon.Default.prototype._getIconUrl;
                    L.Icon.Default.mergeOptions({
                        iconRetinaUrl: '{{ asset('assets/leaflet/images/marker-icon-2x.png') }}',
                        iconUrl: '{{ asset('assets/leaflet/images/marker-icon.png') }}',
                        shadowUrl: '{{ asset('assets/leaflet/images/marker-shadow.png') }}'
                    });

                    const detailMarker = L.marker([{{ $ticket->latitude }}, {{ $ticket->longitude }}]).addTo(detailMap);
                @endif
            });
        }
    @empty
    @endforelse
});

document.addEventListener('DOMContentLoaded', function () {
    // Select all buttons with the class 'resolve-ticket-btn'
    document.querySelectorAll('.resolve-ticket-btn').forEach(button => {
        button.addEventListener('click', function () {
            const ticketId = this.getAttribute('data-ticket-id');
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah aduan ini telah selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Selesai',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                customClass: {
                    container: 'swal2-container' // Ensure the custom z-index class is applied
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form if confirmed
                    const form = document.getElementById(`resolve-form-${ticketId}`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Form tidak ditemukan untuk ticket ID: ' + ticketId);
                    }
                }
            });
        });
    });
});
</script>
@endsection