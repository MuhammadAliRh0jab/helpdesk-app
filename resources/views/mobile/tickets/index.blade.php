@extends('mobile.master.app')

@section('title', 'Semua Tiket')

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
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0">Semua Tiket</h5>
                            <form action="{{ route('tickets.index') }}" method="GET">
                                <select class="pe-4 form-select form-select-sm form-control-clicked" id="statusFilter"
                                    name="status_filter" aria-label="Filter by status" onchange="this.form.submit()">
                                    <option value="" {{ request('status_filter') === '' ? 'selected' : '' }}>Tampilkan
                                        Semua</option>
                                    <option value="0" {{ request('status_filter') === '0' ? 'selected' : '' }}>Belum
                                        Direspon</option>
                                    <option value="1" {{ request('status_filter') === '1' ? 'selected' : '' }}>Direspon
                                    </option>
                                    <option value="2" {{ request('status_filter') === '2' ? 'selected' : '' }}>Selesai
                                    </option>
                                </select>
                            </form>
                        </div>
                        <hr>
                        <div class="table-responsive my-3">
                            <table class="table table-bordered align-items-center align-middle text-center mb-0">
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
                                            <td colspan="8">
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
                                                        <span class="badge bg-warning">Belum Direspon</span>
                                                    @elseif($ticket->status == 1)
                                                        <span class="badge bg-info">Direspon</span>
                                                    @else
                                                        <span class="badge bg-success">Selesai</span>
                                                    @endif
                                                </td>
                                                <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-center">
                                                        <button class="btn m-1 rounded-pill btn-info show-ticket-detail"
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
                                                            Detail
                                                        </button>
                                                        @if ($ticket->status == 1)
                                                            <button
                                                                class="btn m-1 rounded-pill btn-warning show-ticket-responses"
                                                                data-ticket='{{ json_encode([
                                                                    'id' => $ticket->id,
                                                                    'ticket_code' => $ticket->ticket_code,
                                                                    'responses' => $ticket->responses,
                                                                    'status' => $ticket->status,
                                                                    'user_id' => $ticket->user_id,
                                                                ]) }}'>
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
        <!-- Close Button -->
        <button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <!-- Offcanvas Body -->
        <div class="offcanvas-body">
            <h5 class="mb-3" id="ticketDetailModalLabel">Detail Tiket <span id="modal-ticket-code"></span></h5>
            <div class="row">
                <!-- Kolom Kiri -->
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
                <!-- Kolom Kanan -->
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
            <!-- Bagian Gambar -->
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
                    style="max-height: 400px; overflow-y: auto; position: relative;">
                    <!-- Daftar respon akan ditampilkan di sini -->
                    <img class="sticky-bottom" id="imagePreview" src="" alt="No image yet"
                        style="display: none; max-width: 30%; border: 2px solid #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                </div>
                <div class="modal-footer" id="modal-responses-footer">
                    <!-- Form balasan akan ditampilkan di sini jika memenuhi syarat -->
                </div>
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

@endsection

@section('footer')
    @include('mobile.master.footer')
@endsection

@section('styles')
    <style>
        /* Styling untuk modal sukses */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-body {
            padding: 2rem;
        }

        .bi-check-circle {
            color: #28a745;
            /* Bootstrap success green */
        }

        .modal-body .btn-primary {
            min-width: 100px;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Simpan Base64 sementara jika modal/offcanvas belum terbuka
            let pendingImageBase64 = null;

            // Fungsi untuk mengontrol scroll body
            function toggleBodyScroll(enableScroll) {
                if (enableScroll) {
                    $('body').css('overflow', 'auto');
                } else {
                    $('body').css('overflow', 'hidden');
                }
            }

            // Cek query parameter untuk menampilkan modal sukses
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === 'true') {
                // Tampilkan modal
                $('#successModal').modal('show');
                toggleBodyScroll(false);
                // Hapus query parameter 'success' dari URL
                urlParams.delete('success');
                const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() :
                '');
                window.history.replaceState({}, document.title, cleanUrl);
            }

            // Handle penutupan modal sukses
            $('#successModal').on('hidden.bs.modal', function() {
                toggleBodyScroll(true);
                // Fokus kembali ke elemen yang dapat difokuskan pertama
                $('body').find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
                    .first().focus();
            });

            // Offcanvas Detail Tiket
            $('.show-ticket-detail').on('click', function() {
                const ticket = $(this).data('ticket');
                const $triggerButton = $(this);

                $('#modal-ticket-code').text(ticket.ticket_code);
                $('#modal-title').text(ticket.title);
                $('#modal-unit').text(ticket.unit);
                $('#modal-service').text(ticket.service);
                $('#modal-status').text(ticket.status == 0 ? 'Belum Direspon' : (ticket.status == 1 ?
                    'Direspon' : 'Selesai'));
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
                // Disable swipe refresh when offcanvas is opened
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(false);
                }
            });

            // Modal Respon Tiket
            $('.show-ticket-responses').on('click', function() {
                const ticket = $(this).data('ticket');
                const $triggerButton = $(this);
                const currentUserId = {{ auth()->user()->id }};
                const currentUserRoleId = {{ auth()->user()->role_id }};

                // Isi header modal
                $('#modal-responses-ticket-code').text(ticket.ticket_code);

                // Isi daftar respon
                const responsesContainer = $('#modal-responses-body');
                responsesContainer.empty();

                if (ticket.responses && ticket.responses.length > 0) {
                    ticket.responses.forEach((response, index) => {
                        const isLastResponse = index === ticket.responses.length - 1;
                        const isSender = response.user.role_id == 4; // Pengadu (saya) di kanan
                        const chatClass = isSender ? 'single-chat-item outgoing' :
                            'single-chat-item';
                        const sender = response.user.role_id == 2 ? 'Sistem (Operator)' :
                            `${response.user.username} (${response.user.role_id == 4 ? 'Pengadu' : 'PIC'})`;
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

                        // Tambahkan form balasan jika memenuhi syarat
                        if (isLastResponse && currentUserRoleId == 4 && ticket.user_id ==
                            currentUserId && ticket.status != 2 && response.user_id !=
                            currentUserId && response.user.role_id != 2) {
                            const footerContainer = $('#modal-responses-footer');
                            footerContainer.empty();
                            footerContainer.append(`
                            <form action="" method="POST" enctype="multipart/form-data" style="width: 100%;" id="replyForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <textarea name="message" class="form-control mb-2" placeholder="Masukkan balasan Anda..." style="height: 50px;" required></textarea>
                                <div class="form-group mb-2">
                                    <input type="hidden" name="images[]" id="imageBase64Input">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button type="button" id="openCameraBtn" class="btn btn-sm btn-outline-primary me-2">Buka Kamera</button>
                                        <button type="button" id="openGalleryBtn" class="btn btn-sm btn-outline-primary me-2">Pilih dari Galeri</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Kirim Balasan</button>
                                        <input type="file" id="fileFallback" accept="image/*" hidden>
                                    </div>
                                    <p id="errorMessage" class="text-danger text-sm mt-1"></p>
                                </div>
                            </form>
                        `);

                            // Tetapkan action form secara dinamis
                            const replyUrl = "{{ route('tickets.reply', ':response_id') }}"
                                .replace(':response_id', response.id);
                            $('#replyForm').attr('action', replyUrl);
                            console.log('Form action set to:', replyUrl);

                            // Pastikan #imagePreview ada
                            if ($('#imagePreview').length === 0) {
                                console.log('Creating #imagePreview element');
                                $('#modal-responses-body').append(
                                    `<img class="sticky-bottom" id="imagePreview" src="" alt="No image yet" style="display: none; max-width: 30%; border: 2px solid #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">`
                                );
                            }

                            // Event handler untuk tombol kamera dan galeri
                            $('#openCameraBtn').off('click').on('click', function() {
                                console.log('Opening camera');
                                if (typeof AndroidBridge !== 'undefined') {
                                    AndroidBridge.openCamera('showImagePreview');
                                } else {
                                    $('#fileFallback').click(); // Fallback untuk desktop
                                }
                            });

                            $('#openGalleryBtn').off('click').on('click', function() {
                                console.log('Opening photo picker');
                                if (typeof AndroidBridge !== 'undefined') {
                                    AndroidBridge.openPhotoPicker();
                                } else {
                                    $('#fileFallback').click(); // Fallback untuk desktop
                                }
                            });

                            // Fallback untuk desktop
                            $('#fileFallback').on('change', function() {
                                const file = this.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function(e) {
                                        window.showImagePreview(e.target.result);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });

                            // Terapkan Base64 yang tertunda jika ada
                            if (pendingImageBase64) {
                                console.log('Applying pending image base64');
                                window.showImagePreview(pendingImageBase64);
                                pendingImageBase64 = null;
                            }
                        } else {
                            const footerContainer = $('#modal-responses-footer');
                            footerContainer.empty();
                            footerContainer.append(`
                            <div class="text-dark text-center w-100">Menunggu balasan PIC terkait</div>
                        `);
                        }
                    });
                } else {
                    responsesContainer.append(
                        '<p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>');
                    const footerContainer = $('#modal-responses-footer');
                    footerContainer.empty();
                    footerContainer.append(
                        '<div class="text-dark text-center w-100">Menunggu balasan PIC terkait</div>');
                }

                toggleBodyScroll(false);
                console.log('Modal show triggered, checking #imagePreview:', $('#imagePreview').length);
                // Disable swipe refresh when modal is opened
                if (typeof window.Android !== 'undefined') {
                    window.Android.toggleSwipeRefresh(false);
                }
                $('#ticketResponsesModal').modal('show');
            });

            // Kelola fokus saat offcanvas/modal ditutup
            $('#ticketDetailModal').on('hidden.bs.offcanvas', function() {
                toggleBodyScroll(true);
                // Re-enable swipe refresh when offcanvas is closed
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
                $('#imagePreview').css('display', 'none'); // Sembunyikan pratinjau saat modal ditutup
                pendingImageBase64 = null; // Reset Base64 tertunda
                // Re-enable swipe refresh when modal is closed
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

            // Kelola fokus saat offcanvas/modal dibuka
            $('#ticketDetailModal').on('shown.bs.offcanvas', function() {
                $(this).find('.btn-close').focus();
            });

            $('#ticketResponsesModal').on('shown.bs.modal', function() {
                $(this).find('.btn-close').focus();
                console.log('Modal shown, #imagePreview exists:', $('#imagePreview').length);
                // Terapkan Base64 yang tertunda jika ada
                if (pendingImageBase64) {
                    console.log('Modal shown, applying pending image base64');
                    window.showImagePreview(pendingImageBase64);
                    pendingImageBase64 = null;
                }
            });

            // Handle submit form balasan via AJAX
            $(document).on('submit', '#replyForm', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(this);

                // Jika ada Base64, konversi ke file blob
                const base64Image = $('#imageBase64Input').val();
                if (base64Image) {
                    try {
                        const byteString = atob(base64Image.split(',')[1]); // Hapus header Base64
                        const arrayBuffer = new ArrayBuffer(byteString.length);
                        const uint8Array = new Uint8Array(arrayBuffer);
                        for (let i = 0; i < byteString.length; i++) {
                            uint8Array[i] = byteString.charCodeAt(i);
                        }
                        const blob = new Blob([uint8Array], {
                            type: 'image/jpeg'
                        });
                        formData.set('images[]', blob, 'photo.jpg');
                        console.log('Image blob size:', blob.size / 1024, 'KB');
                    } catch (e) {
                        console.log('Error converting Base64 to blob:', e);
                        $('#errorMessage').text('Error converting image');
                        return;
                    }
                }

                $.ajax({
                    url: form.attr('action'), // Gunakan action dari form
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('Reply submitted successfully:', response);
                        $('#ticketResponsesModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        console.log('Error submitting reply:', xhr.responseJSON);
                        const errors = xhr.responseJSON.errors || {};
                        let errorMessage = 'Error: ';
                        for (let field in errors) {
                            errorMessage += errors[field][0] + ' ';
                        }
                        $('#errorMessage').text(errorMessage || 'Unknown error occurred');
                    }
                });
            });

            // Fungsi untuk menampilkan pratinjau gambar dari AndroidBridge
            window.showImagePreview = function(base64Image) {
                console.log('Showing image preview with base64 length:', base64Image.length);
                let $preview = $('#imagePreview');
                const $modal = $('#ticketResponsesModal');
                const $input = $('#imageBase64Input');

                // Jika #imagePreview tidak ada, buat elemen baru
                if ($preview.length === 0) {
                    console.warn('#imagePreview not found, creating new one');
                    $('#modal-responses-body').append(
                        `<img class="sticky-bottom" id="imagePreview" src="" alt="No image yet" style="display: none; max-width: 30%; border: 2px solid #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">`
                    );
                    $preview = $('#imagePreview');
                }

                if ($preview.length && $modal.is(':visible') && $input.length) {
                    console.log('Modal is visible, updating preview');
                    $preview.attr('src', base64Image);
                    $preview.css('display', 'block');
                    $input.val(base64Image);
                } else {
                    console.warn('Modal not visible or elements not found, storing base64 for later');
                    console.log('Preview exists:', $preview.length, 'Modal visible:', $modal.is(':visible'),
                        'Input exists:', $input.length);
                    pendingImageBase64 = base64Image;
                    // Coba ulang setelah delay kecil
                    setTimeout(() => {
                        console.log('Retrying showImagePreview after delay');
                        $preview = $('#imagePreview');
                        if ($preview.length && $modal.is(':visible') && $input.length) {
                            $preview.attr('src', base64Image);
                            $preview.css('display', 'block');
                            $input.val(base64Image);
                            pendingImageBase64 = null;
                        }
                    }, 500);
                }
            };
        });
    </script>
@endsection
