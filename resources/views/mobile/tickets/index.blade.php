@extends('mobile.master.app')

@section('title', 'Daftar Aduan')

@section('header')
    @include('mobile.master.header')
@endsection

@section('sidenav')
    @include('mobile.master.sidenav')
@endsection

@section('content')
    <div class="page-content-wrapper py-3">
        <div class="container">
            <div class="affan-element-item">
                <div class="element-heading-wrapper">
                    <i class="bi bi-ticket"></i>
                    <div class="heading-text">
                        <h5 class="mb-1">Manajemen Tiket</h5>
                        <span>Semua status tiket ditampilkan dibawah</span>
                    </div>
                </div>
            </div>

            <div class="py-2">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="card-title m-0">Daftar Aduan</h5>
                        </div>
                        <!-- Search and Filter -->
                        <form action="{{ route('tickets.index') }}" method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-7">
                                    <div class="input-group mb-2">
                                        <input type="text" name="search" class="form-control search-input"
                                            placeholder="Cari tiket..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-5">
                                    <select class="pe-4 form-select" id="statusFilter" name="status_filter"
                                        aria-label="Filter by status" onchange="this.form.submit()">
                                        <option value="" {{ request('status_filter') === '' ? 'selected' : '' }}>
                                            Semua</option>
                                        <option value="0" {{ request('status_filter') === '0' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="1" {{ request('status_filter') === '1' ? 'selected' : '' }}>
                                            Ditugaskan
                                        </option>
                                        <option value="2" {{ request('status_filter') === '2' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="table-responsive my-3">
                            <table class="table table-bordered align-items-center align-middle text-center mb-0"
                                id="ticket-table">
                                <thead>
                                    <tr class="text-nowrap">
                                        <th scope="col">#</th>
                                        <th>No Tiket</th>
                                        <th>Judul Aduan</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($tickets->isEmpty())
                                        <tr>
                                            <td colspan="7">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="alert alert-info m-0">
                                                            Anda belum memiliki tiket. Silahkan buka halaman tiket dan klik
                                                            Buat Aduan untuk membuat tiket.
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($tickets as $ticket)
                                            <tr class="text-nowrap"
                                                data-status="{{ $ticket->status == 0 ? 'belum' : ($ticket->status == 1 ? 'direspon' : 'selesai') }}">
                                                <td class="ticket-number">
                                                    {{ ($tickets->currentPage() - 1) * $tickets->perPage() + $loop->iteration }}
                                                </td>
                                                <td>{{ $ticket->ticket_code }}</td>
                                                <td>{{ $ticket->title }}</td>
                                                <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                                <td>
                                                    @if ($ticket->status == 0)
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($ticket->status == 1)
                                                        <span class="badge bg-info">Ditugaskan</span>
                                                    @else
                                                        <span class="badge bg-success">Selesai</span>
                                                    @endif
                                                </td>
                                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-center">
                                                        <button class="btn btn-sm m-1 btn-warning show-ticket-detail"
                                                            data-ticket='{{ json_encode([
                                                                'ticket_code' => $ticket->ticket_code,
                                                                'title' => $ticket->title,
                                                                'unit' => $ticket->original_unit_id
                                                                    ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name ?? 'Tidak ditentukan'
                                                                    : $ticket->unit->unit_name ?? 'Tidak ditentukan',
                                                                'service' => $ticket->service->svc_name ?? 'Tidak ditentukan',
                                                                'status' => $ticket->status,
                                                                'created_at' => $ticket->created_at,
                                                                'description' => $ticket->description,
                                                                'uploads' => $ticket->uploads,
                                                            ]) }}'
                                                            data-bs-toggle="offcanvas" data-bs-target="#ticketDetailModal">
                                                            <i class="fa fa-eye me-1"></i>
                                                            Detail
                                                        </button>
                                                        @if ($ticket->status == 1)
                                                            <button class="btn btn-sm m-1 btn-info show-ticket-responses"
                                                                data-ticket='{{ json_encode([
                                                                    'id' => $ticket->id,
                                                                    'ticket_code' => $ticket->ticket_code,
                                                                    'responses' => $ticket->responses,
                                                                    'status' => $ticket->status,
                                                                    'user_id' => $ticket->user_id,
                                                                ]) }}'>
                                                                <i class="fa fa-comments me-1"></i>
                                                                Respon
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <span class="d-flex justify-content-end">
                            {{ $tickets->appends(request()->except('success'))->links() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Detail Tiket -->
    <div class="offcanvas offcanvas-bottom" id="ticketDetailModal" tabindex="-1" aria-labelledby="ticketDetailModalLabel">
        <button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <div class="offcanvas-body">
            <h5 class="mb-3" id="ticketDetailModalLabel">Detail Tiket <span id="modal-ticket-code"></span></h5>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Judul Aduan</p>
                        <p class="m-0 text-muted" id="modal-title"></p>
                    </div>
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Unit</p>
                        <p class="m-0 text-muted" id="modal-unit"></p>
                    </div>
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Layanan</p>
                        <p class="m-0 text-muted" id="modal-service"></p>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Status</p>
                        <p class="m-0 text-muted" id="modal-status"></p>
                    </div>
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Dibuat</p>
                        <p class="m-0 text-muted" id="modal-created"></p>
                    </div>
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Deskripsi</p>
                        <p class="m-0 text-muted" id="modal-description"></p>
                    </div>
                </div>
            </div>
            <div class="mt-4" id="modal-uploads">
                <h6 class="mb-2">Gambar</h6>
            </div>
        </div>
    </div>

    <!-- Modal Respon Tiket (Chat Bubble Style) -->
    <div class="modal fade" id="ticketResponsesModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="ticketResponsesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="ticketResponsesModalLabel">Percakapan Tiket <span
                            id="modal-responses-ticket-code"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-responses-body"
                    style="max-height: 400px; overflow-y: auto; position: relative">
                    <!-- Responses will be appended here -->
                </div>
                <div class="modal-footer" id="modal-responses-footer"></div>
            </div>
        </div>
    </div>

    <!-- Modal Sukses -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-check-circle text-success mb-3" style="font-size: 5rem;"></i>
                    <h5 id="successModalLabel">Sukses</h5>
                    <p>Aduan Berhasil Dibuat</p>
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .image-preview-container {
            position: sticky;
            bottom: 0;
            background: #ffffff00;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            z-index: 10;
        }

        .image-preview-container .position-relative {
            position: relative;
        }

        .image-preview-container img {
            object-fit: cover;
            max-width: 100px;
            height: auto;
            border: 2px solid #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .image-preview-container .btn-danger {
            border-radius: 50%;
            padding: 2px 6px;
            line-height: 1;
            position: absolute;
            top: -10px;
            right: -10px;
            z-index: 11;
        }
    </style>
@endsection

@section('footer')
    @include('mobile.master.footer')
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        $(document).ready(function() {
            function toggleBodyScroll(enableScroll) {
                $('body').css('overflow', enableScroll ? 'auto' : 'hidden');
            }

            function updateTicketNumbers() {
                $('#ticket-table tbody tr:not([style*="display: none"])').each(function(index) {
                    $(this).find('.ticket-number').text(index + 1);
                });
            }

            function countPengaduMessagesSinceLastPegawai(responses, currentUserId) {
                console.log('Calculating pengadu messages. Responses:', responses);
                let lastPegawaiResponseTime = null;
                for (let i = responses.length - 1; i >= 0; i--) {
                    if (responses[i].user && responses[i].user.role_id == 3) {
                        lastPegawaiResponseTime = new Date(responses[i].created_at).getTime();
                        console.log('Last PIC response found at:', responses[i].created_at);
                        break;
                    }
                }
                if (lastPegawaiResponseTime === null) {
                    const count = responses.filter(response => response.user_id == currentUserId).length;
                    console.log('No PIC response. Total pengadu messages:', count);
                    return count;
                }
                const count = responses.filter(response =>
                    response.user_id == currentUserId &&
                    new Date(response.created_at).getTime() > lastPegawaiResponseTime
                ).length;
                console.log('Pengadu messages since last PIC:', count);
                return count;
            }

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === 'true') {
                $('#successModal').modal('show');
                toggleBodyScroll(false);
                urlParams.delete('success');
                const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() :
                    '');
                window.history.replaceState({}, document.title, cleanUrl);
            }

            $('#successModal').on('hidden.bs.modal', function() {
                toggleBodyScroll(true);
                $('body').find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
                    .first().focus();
            });

            $('.show-ticket-detail').on('click', function() {
                const ticket = $(this).data('ticket');
                const $triggerButton = $(this);

                $('#modal-ticket-code').text(ticket.ticket_code);
                $('#modal-title').text(ticket.title);
                $('#modal-unit').text(ticket.unit);
                $('#modal-service').text(ticket.service);
                $('#modal-status').text(ticket.status == 0 ? 'Pending' : (ticket.status == 1 ?
                    'Ditugaskan' : 'Selesai'));
                $('#modal-created').text(new Date(ticket.created_at).toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }));
                $('#modal-description').text(ticket.description || 'Tidak ada deskripsi');

                const uploadsContainer = $('#modal-uploads');
                uploadsContainer.empty();
                uploadsContainer.append('<h6 class="mb-2">Gambar</h6>');
                if (ticket.uploads && ticket.uploads.length > 0) {
                    ticket.uploads.forEach(upload => {
                        uploadsContainer.append(
                            `<img src="{{ asset('storage') }}/${upload.filename_path}" alt="Upload" class="img-fluid mb-2" style="max-width: 100%;">`
                        );
                    });
                } else {
                    uploadsContainer.append('<p class="text-muted">Tidak ada gambar.</p>');
                }

                toggleBodyScroll(false);
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(false);
                }
            });

            $('.show-ticket-responses').on('click', function() {
                const ticket = $(this).data('ticket');
                const currentUserId = {{ auth()->user()->id }};
                const currentUserRoleId = {{ auth()->user()->role_id }};

                console.log('Ticket data:', ticket);
                console.log('Current user ID:', currentUserId, 'Role ID:', currentUserRoleId);

                $('#modal-responses-ticket-code').text(ticket.ticket_code);
                const responsesContainer = $('#modal-responses-body');
                const footerContainer = $('#modal-responses-footer');
                responsesContainer.empty();
                footerContainer.empty();

                const pengaduMessagesSinceLastPegawai = ticket.responses && ticket.responses.length > 0 ?
                    countPengaduMessagesSinceLastPegawai(ticket.responses, currentUserId) : 0;

                console.log('Pengadu messages since last PIC:', pengaduMessagesSinceLastPegawai);

                if (ticket.responses && ticket.responses.length > 0) {
                    // Sort responses by created_at (ascending)
                    const sortedResponses = ticket.responses.slice().sort((a, b) =>
                        new Date(a.created_at) - new Date(b.created_at));

                    sortedResponses.forEach((response, index) => {
                        const isSender = response.user && response.user.role_id == 4;
                        const chatClass = isSender ? 'single-chat-item outgoing' :
                            'single-chat-item';
                        const sender = response.user && response.user.role_id == 2 ?
                            'Sistem (Operator)' :
                            response.user ?
                            `${response.user.username} (${response.user.role_id == 4 ? 'Pengadu' : 'PIC'})` :
                            'Unknown';
                        const createdAt = new Date(response.created_at).toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        let responseHtml = `
                            <div class="${chatClass}">
                                <div class="user-message">
                                    <div class="message-sender-name">
                                        <div class="sender-name">${sender}</div>
                                    </div>
                                    <div class="message-content">
                                        <div class="single-message">
                                            <p>${response.message || 'Tidak ada pesan'}</p>
                                        </div>
                        `;

                        if (response.uploads && response.uploads.length > 0) {
                            response.uploads.forEach(upload => {
                                responseHtml += `
                                    <div class="single-message">
                                        <div class="gallery-img">
                                            <a href="{{ asset('storage') }}/${upload.filename_path}">
                                                <img src="{{ asset('storage') }}/${upload.filename_path}" alt="${upload.filename_ori}">
                                            </a>
                                        </div>
                                    </div>
                                `;
                            });
                        }

                        responseHtml += `
                                    </div>
                                    <div class="message-time-status">
                                        <div class="sent-time">${createdAt}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        responsesContainer.append(responseHtml);
                    });
                } else {
                    responsesContainer.append(
                        '<p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>'
                    );
                }

                // Add image preview container
                responsesContainer.append(`
                    <div class="image-preview-container" id="imagePreviewContainer" x-data="{
                        images: [],
                        addImage(base64Image) {
                            this.images.push(base64Image);
                            console.log('Image added, total images:', this.images.length);
                        },
                        removeImage(index) {
                            this.images.splice(index, 1);
                            console.log('Image removed, total images:', this.images.length);
                        }
                    }">
                        <template x-for="(image, index) in images" :key="index">
                            <div class="position-relative">
                                <img :src="image" alt="Image preview" class="img-preview">
                                <button type="button" @click="removeImage(index)" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                `);

                console.log('Evaluating form display:', {
                    currentUserRoleId,
                    ticketUserId: ticket.user_id,
                    currentUserId,
                    ticketStatus: ticket.status,
                    hasPicResponse: ticket.responses.some(response => response.user && response.user
                        .role_id == 3),
                    pengaduMessagesSinceLastPegawai
                });

                if (currentUserRoleId == 4 &&
                    ticket.user_id == currentUserId &&
                    ticket.status != 2 &&
                    pengaduMessagesSinceLastPegawai < 10 &&
                    ticket.responses.some(response => response.user && response.user.role_id == 3)) {
                    footerContainer.append(`
                        <form action="" method="POST" enctype="multipart/form-data" style="width: 100%;" id="replyForm">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <textarea name="message" class="form-control mb-2" placeholder="Masukkan balasan Anda..." style="min-height: 50px;" required></textarea>
                            <div class="form-group mb-2">
                                <input type="hidden" name="images[]" id="imageBase64Input" x-bind:value="JSON.stringify($refs.imagePreviewContainer.__x.$data.images)">
                                <div class="d-flex align-items-center justify-content-center">
                                    <button type="button" id="openCameraBtn" class="btn btn-sm btn-outline-primary me-2" aria-label="Buka Kamera">Buka Kamera</button>
                                    <button type="button" id="openGalleryBtn" class="btn btn-sm btn-outline-primary me-2" aria-label="Pilih dari Galeri">Pilih dari Galeri</button>
                                    <button type="submit" class="btn btn-sm btn-primary" aria-label="Kirim Balasan">Kirim Balasan</button>
                                    <input type="file" id="fileFallback" accept="image/*" multiple hidden>
                                </div>
                                <p id="errorMessage" class="text-danger text-sm mt-1"></p>
                            </div>
                        </form>
                    `);

                    const replyUrl = "{{ route('tickets.reply', ':ticket_id') }}".replace(':ticket_id',
                        ticket.id);
                    $('#replyForm').attr('action', replyUrl);

                    $('#openCameraBtn').off('click').on('click', function() {
                        if (typeof AndroidBridge !== 'undefined') {
                            AndroidBridge.openCamera('showImagePreview');
                        } else {
                            $('#fileFallback').click();
                        }
                    });

                    $('#openGalleryBtn').off('click').on('click', function() {
                        if (typeof AndroidBridge !== 'undefined') {
                            AndroidBridge.openPhotoPicker('showImagePreview');
                        } else {
                            $('#fileFallback').click();
                        }
                    });

                    $('#fileFallback').on('change', function() {
                        const files = this.files;
                        if (files.length > 0) {
                            const instance = Alpine.$data(document.getElementById(
                                'imagePreviewContainer'));
                            Array.from(files).forEach(file => {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    instance.addImage(e.target.result);
                                };
                                reader.readAsDataURL(file);
                            });
                            this.value = ''; // Reset file input
                        }
                    });
                } else {
                    const message = pengaduMessagesSinceLastPegawai >= 10 ?
                        'Anda telah mencapai batas 10 balasan. Tunggu respons PIC untuk melanjutkan.' :
                        'Menunggu balasan PIC terkait';
                    footerContainer.append(
                        `<div class="text-dark text-center w-100" role="alert">${message}</div>`
                    );
                }

                toggleBodyScroll(false);
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(false);
                }
                $('#ticketResponsesModal').modal('show');
                // Scroll to bottom
                responsesContainer.scrollTop(responsesContainer[0].scrollHeight);
            });

            $('#ticketDetailModal').on('hidden.bs.offcanvas', function() {
                toggleBodyScroll(true);
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(true);
                }
                const $trigger = $('.show-ticket-detail:focus');
                if ($trigger.length) {
                    $trigger.focus();
                } else {
                    $('body').find(
                            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
                        .first().focus();
                }
            });

            $('#ticketResponsesModal').on('hidden.bs.modal', function() {
                toggleBodyScroll(true);
                const previewContainer = $('#imagePreviewContainer');
                if (previewContainer.length) {
                    const instance = Alpine.$data(previewContainer[0]);
                    instance.images = []; // Clear images
                }
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(true);
                }
                const $trigger = $('.show-ticket-responses:focus');
                if ($trigger.length) {
                    $trigger.focus();
                } else {
                    $('body').find(
                            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
                        .first().focus();
                }
            });

            $('#ticketDetailModal').on('shown.bs.offcanvas', function() {
                $(this).find('.btn-close').focus();
            });

            $('#ticketResponsesModal').on('shown.bs.modal', function() {
                $(this).find('.btn-close').focus();
                const responsesContainer = $('#modal-responses-body');
                responsesContainer.scrollTop(responsesContainer[0].scrollHeight);
            });

            $(document).on('submit', '#replyForm', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(this);
                const ticketId = form.attr('action').match(/\/tickets\/(\d+)\/reply/)[1];
                const responsesContainer = $('#modal-responses-body');
                const errorMessage = $('#errorMessage');
                const previewContainer = $('#imagePreviewContainer');
                const instance = Alpine.$data(previewContainer[0]);
                const images = instance.images;

                // Convert Base64 images to blobs
                images.forEach((base64Image, index) => {
                    try {
                        const byteString = atob(base64Image.split(',')[1]);
                        const arrayBuffer = new ArrayBuffer(byteString.length);
                        const uint8Array = new Uint8Array(arrayBuffer);
                        for (let i = 0; i < byteString.length; i++) {
                            uint8Array[i] = byteString.charCodeAt(i);
                        }
                        const blob = new Blob([uint8Array], {
                            type: 'image/jpeg'
                        });
                        formData.append('images[]', blob, `photo-${index}.jpg`);
                    } catch (e) {
                        errorMessage.text('Error converting image ' + index);
                        return;
                    }
                });

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Clear the form
                        form.find('textarea[name="message"]').val('');
                        instance.images = [];
                        errorMessage.text('');

                        // Get current user
                        const currentUser = @json(auth()->user() ?? null);
                        if (!currentUser) {
                            errorMessage.text('User not authenticated. Please log in.');
                            return;
                        }

                        // Append new response
                        const createdAt = new Date().toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        let responseHtml = `
                            <div class="single-chat-item outgoing">
                                <div class="user-message">
                                    <div class="message-sender-name">
                                        <div class="sender-name">${currentUser.username} (Pengadu)</div>
                                    </div>
                                    <div class="message-content">
                                        <div class="single-message">
                                            <p>${formData.get('message') || 'Tidak ada pesan'}</p>
                                        </div>
                        `;

                        if (images.length > 0) {
                            images.forEach((base64Image, index) => {
                                responseHtml += `
                                    <div class="single-message">
                                        <div class="gallery-img">
                                            <a href="${base64Image}">
                                                <img src="${base64Image}" alt="Uploaded image ${index}">
                                            </a>
                                        </div>
                                    </div>
                                `;
                            });
                        }

                        responseHtml += `
                                    </div>
                                    <div class="message-time-status">
                                        <div class="sent-time">${createdAt}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        responsesContainer.append(responseHtml);
                        responsesContainer.scrollTop(responsesContainer[0].scrollHeight);

                        // Update ticket data
                        const ticketButton = $(
                            `.show-ticket-responses[data-ticket*='"id":${ticketId}']`);
                        if (ticketButton.length) {
                            const ticketData = JSON.parse(ticketButton.attr('data-ticket'));
                            ticketData.responses.push({
                                user: currentUser,
                                user_id: currentUser.id,
                                message: formData.get('message'),
                                created_at: new Date().toISOString(),
                                uploads: images.map((img, index) => ({
                                    filename_path: `temp/photo-${index}.jpg`,
                                    filename_ori: `photo-${index}.jpg`
                                }))
                            });
                            ticketButton.attr('data-ticket', JSON.stringify(ticketData));

                            // Check message limit
                            const pengaduMessagesSinceLastPegawai =
                                countPengaduMessagesSinceLastPegawai(ticketData.responses,
                                    currentUser.id);
                            if (pengaduMessagesSinceLastPegawai >= 10) {
                                $('#modal-responses-footer').html(
                                    '<div class="text-dark text-center w-100" role="alert">Anda telah mencapai batas 10 balasan. Tunggu respons PIC untuk melanjutkan.</div>'
                                );
                            }
                        }

                        // Update ticket status
                        const ticketRow = $(
                            `#ticket-table tr[data-status][data-ticket*='"id":${ticketId}']`
                        );
                        if (ticketRow.length && response.ticket && response.ticket.status !==
                            undefined) {
                            const statusBadge = ticketRow.find('td:nth-child(5) .badge');
                            if (response.ticket.status == 0) {
                                statusBadge.removeClass('bg-info bg-success').addClass(
                                    'bg-warning').text('Pending');
                            } else if (response.ticket.status == 1) {
                                statusBadge.removeClass('bg-warning bg-success').addClass(
                                    'bg-info').text('Ditugaskan');
                            } else {
                                statusBadge.removeClass('bg-warning bg-info').addClass(
                                    'bg-success').text('Selesai');
                            }
                        }
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors || {};
                        let errorMessageText = 'Error: ';
                        for (let field in errors) {
                            errorMessageText += errors[field][0] + ' ';
                        }
                        errorMessage.text(errorMessageText || 'Unknown error occurred');
                    }
                });
            });

            window.showImagePreview = function(base64Image) {
                console.log('Showing image preview with base64 length:', base64Image.length);
                const previewContainer = $('#imagePreviewContainer');
                if (previewContainer.length) {
                    const instance = Alpine.$data(previewContainer[0]);
                    instance.addImage(base64Image);
                } else {
                    console.warn('Image preview container not found');
                    $('#errorMessage').text('Error: Preview container not found');
                }
            };

            // Update ticket numbers on page load
            updateTicketNumbers();
        });
    </script>
@endsection
