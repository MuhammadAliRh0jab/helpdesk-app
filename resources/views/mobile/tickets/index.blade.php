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
                        <a class="btn rounded-pill btn-primary" href="{{ route('tickets.create') }}">Buat Aduan</a>
                    </div>
                    <hr>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-items-center align-middle text-center mb-0">
                            <thead>
                                <tr class="text-nowrap">
                                    <th scope="col">#</th>
                                    <th>No Tiket</th>
                                    <th>Judul Aduan</th>
                                    <th>Unit</th>
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
                                                        Anda belum memiliki tiket. Silahkan buka halaman tiket dan klik Buat Aduan untuk membuat tiket.
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($tickets as $ticket)
                                        <tr class="text-nowrap">
                                            <th>{{ $loop->iteration }}</th>
                                            <td>{{ $ticket->ticket_code }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name ?? 'Tidak ditentukan' : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                                            <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                            <td>
                                                @if($ticket->status == 0) Belum Direspon
                                                @elseif($ticket->status == 1) Direspon
                                                @else Selesai
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex flex-column align-items-center">
                                                    <button class="btn m-1 rounded-pill btn-info show-ticket-detail"
                                                            data-ticket='{{ json_encode([
                                                                "ticket_code" => $ticket->ticket_code,
                                                                "title" => $ticket->title,
                                                                "unit" => $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name ?? "Tidak ditentukan" : ($ticket->unit->unit_name ?? "Tidak ditentukan"),
                                                                "service" => $ticket->service->svc_name ?? "Tidak ditentukan",
                                                                "status" => $ticket->status,
                                                                "created_at" => $ticket->created_at,
                                                                "description" => $ticket->description,
                                                                "uploads" => $ticket->uploads,
                                                                "responses" => $ticket->responses
                                                            ]) }}'>
                                                        Detail
                                                    </button>
                                                    @if($ticket->status == 1)
                                                        <button class="btn m-1 rounded-pill btn-warning show-ticket-responses"
                                                                data-ticket='{{ json_encode([
                                                                    "id" => $ticket->id,
                                                                    "ticket_code" => $ticket->ticket_code,
                                                                    "responses" => $ticket->responses,
                                                                    "status" => $ticket->status,
                                                                    "user_id" => $ticket->user_id
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Tiket -->
<div class="modal fade" id="ticketDetailModal" tabindex="-1" aria-labelledby="ticketDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ticketDetailModalLabel">Detail Tiket <span id="modal-ticket-code"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-start align-middle">
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Judul Aduan</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-title"></p></div>
                    <div class="w-100"></div>
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Unit</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-unit"></p></div>
                    <div class="w-100"></div>
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Layanan</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-service"></p></div>
                    <div class="w-100"></div>
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Status</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-status"></p></div>
                    <div class="w-100"></div>
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Dibuat</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-created"></p></div>
                    <div class="w-100"></div>
                    <div class="col-4"><p class="m-0 text-dark fw-bold">Deskripsi</p></div>
                    <div class="col-8"><p class="m-0 text-dark" id="modal-description"></p></div>
                    <div class="w-100"></div>
                    <div id="modal-uploads" class="mt-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn rounded-pill btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Respon Tiket (Chat Bubble Style) -->
<div class="modal fade" id="ticketResponsesModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="ticketResponsesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="ticketResponsesModalLabel">Percakapan Tiket <span id="modal-responses-ticket-code"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-responses-body" style="max-height: 400px; overflow-y: auto; position: relative;">
                <!-- Daftar respon akan ditampilkan di sini -->
                <img class="m-1" id="imagePreview" src="" alt="No image yet" style="display: none; max-width: 150px; position: absolute; bottom: 10px; left: 10px; z-index: 10; border: 2px solid #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            </div>
            <div class="modal-footer" id="modal-responses-footer">
                <!-- Form balasan akan ditampilkan di sini jika memenuhi syarat -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
  @include('mobile.master.footer')
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="{{ asset('mobile/js/android-bridge.js') }}"></script>
<script>
    $(document).ready(function() {
        // Fungsi untuk mengontrol scroll body
        function toggleBodyScroll(enableScroll) {
            if (enableScroll) {
                $('body').css('overflow', 'auto');
            } else {
                $('body').css('overflow', 'hidden');
            }
        }

        // Modal Detail Tiket
        $('.show-ticket-detail').on('click', function() {
            const ticket = $(this).data('ticket');
            const $triggerButton = $(this);

            $('#modal-ticket-code').text(ticket.ticket_code);
            $('#modal-title').text(ticket.title);
            $('#modal-unit').text(ticket.unit);
            $('#modal-service').text(ticket.service);
            $('#modal-status').text(ticket.status == 0 ? 'Belum Direspon' : (ticket.status == 1 ? 'Direspon' : 'Selesai'));
            $('#modal-created').text(new Date(ticket.created_at).toLocaleString('id-ID', {
                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
            }));
            $('#modal-description').text(ticket.description || 'Tidak ada deskripsi');

            const uploadsContainer = $('#modal-uploads');
            uploadsContainer.empty();
            if (ticket.uploads && ticket.uploads.length > 0) {
                uploadsContainer.append('<p class="m-0 text-dark fw-bold">Gambar:</p>');
                ticket.uploads.forEach(upload => {
                    uploadsContainer.append(
                        `<img src="{{ asset('storage') }}/${upload.filename_path}" alt="Upload" class="img-fluid mb-2" style="max-width: 300px;">`
                    );
                });
            }

            toggleBodyScroll(false);
            $('#ticketDetailModal').modal('show');
        });

        // Modal Respon Tiket (Chat Bubble Style)
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
                    const bubbleClass = isSender ? 'right' : 'left';
                    const sender = response.user.role_id == 2 ? 'Sistem (Operator)' : `${response.user.username} (${response.user.role_id == 4 ? 'Pengadu' : 'PIC'})`;
                    const createdAt = new Date(response.created_at).toLocaleString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                    let responseHtml = `
                        <div class="chat-bubble ${bubbleClass} text-dark">
                            <div class="time">${createdAt}</div>
                            <div class="sender">${sender}</div>
                            <div class="message">${response.message || 'Tidak ada pesan'}</div>
                            <div class="uploads">
                    `;

                    if (response.uploads && response.uploads.length > 0) {
                        response.uploads.forEach(upload => {
                            responseHtml += `
                                <img src="{{ asset('storage') }}/${upload.filename_path}" alt="${upload.filename_ori}">
                            `;
                        });
                    }

                    responseHtml += `</div></div>`;
                    responsesContainer.append(responseHtml);

                    // Tambahkan form balasan jika memenuhi syarat
                    if (isLastResponse && currentUserRoleId == 4 && ticket.user_id == currentUserId && ticket.status != 2 && response.user_id != currentUserId && response.user.role_id != 2) {
                        const footerContainer = $('#modal-responses-footer');
                        footerContainer.empty();
                        footerContainer.append(`
                            <form action="{{ route('tickets.reply', '') }}/${response.id}" method="POST" enctype="multipart/form-data" class="w-100 p-2" id="replyForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <textarea name="message" class="form-control form-control-clicked mb-2" placeholder="Masukkan balasan Anda..." style="height: 50px;" required></textarea>
                                <div class="form-group mb-2">
                                    <input type="hidden" name="images[]" id="imageBase64Input">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button type="button" id="openCameraBtn" class="btn btn-sm btn-outline-primary me-2">Buka Kamera</button>
                                        <button type="button" id="openGalleryBtn" class="btn btn-sm btn-outline-primary me-2">Pilih dari Galeri</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Kirim Balasan</button>
                                    </div>
                                    <p id="errorMessage" class="text-danger text-sm mt-1"></p>
                                </div>
                            </form>
                        `);

                        // Event handler untuk tombol kamera dan galeri
                        $('#openCameraBtn').off('click').on('click', function() {
                            console.log('Opening camera');
                            // Pastikan modal terbuka sebelum memanggil AndroidBridge
                            if ($('#ticketResponsesModal').is(':visible')) {
                                AndroidBridge.openCamera('showImagePreview');
                            } else {
                                $('#ticketResponsesModal').one('shown.bs.modal', function() {
                                    AndroidBridge.openCamera('showImagePreview');
                                });
                            }
                        });

                        $('#openGalleryBtn').off('click').on('click', function() {
                            console.log('Opening photo picker');
                            // Pastikan modal terbuka sebelum memanggil AndroidBridge
                            if ($('#ticketResponsesModal').is(':visible')) {
                                AndroidBridge.openPhotoPicker();
                            } else {
                                $('#ticketResponsesModal').one('shown.bs.modal', function() {
                                    AndroidBridge.openPhotoPicker();
                                });
                            }
                        });
                    } else {
                        const footerContainer = $('#modal-responses-footer');
                        footerContainer.empty();
                        footerContainer.append(`
                            <div class="text-dark text-center w-100">Menunggu balasan PIC terkait</div>
                        `);
                    }
                });
            } else {
                responsesContainer.append('<p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>');
                const footerContainer = $('#modal-responses-footer');
                footerContainer.empty();
                footerContainer.append('<div class="text-dark text-center w-100">Menunggu balasan PIC terkait</div>');
            }

            toggleBodyScroll(false);
            $('#ticketResponsesModal').modal('show');
        });

        // Kelola fokus saat modal ditutup
        $('#ticketDetailModal, #ticketResponsesModal').on('hidden.bs.modal', function () {
            toggleBodyScroll(true);
            $('#imagePreview').css('display', 'none'); // Sembunyikan pratinjau saat modal ditutup
            const $trigger = $('.show-ticket-detail:focus, .show-ticket-responses:focus');
            if ($trigger.length) {
                $trigger.focus();
            } else {
                $('body').find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').first().focus();
            }
        });

        // Kelola fokus saat modal dibuka
        $('#ticketDetailModal, #ticketResponsesModal').on('shown.bs.modal', function () {
            $(this).find('.btn-close').focus();
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
                    const blob = new Blob([uint8Array], { type: 'image/jpeg' });
                    formData.set('images[]', blob, 'photo.jpg');
                    console.log('Image blob size:', blob.size / 1024, 'KB');
                } catch (e) {
                    console.log('Error converting Base64 to blob:', e);
                    $('#errorMessage').text('Error converting image');
                    return;
                }
            }

            $.ajax({
                url: form.action,
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
            console.log('Showing image preview with base64:', base64Image.substring(0, 50) + '...');
            const $preview = $('#imagePreview');
            const $modal = $('#ticketResponsesModal');
            if ($preview.length && $modal.is(':visible')) {
                $preview.attr('src', base64Image);
                $preview.css('display', 'block');
                $('#imageBase64Input').val(base64Image);
            } else {
                console.error('Element #imagePreview not found or modal not visible');
                console.log('Preview exists:', $preview.length, 'Modal visible:', $modal.is(':visible'));
                // Tunggu modal terbuka jika belum
                $modal.one('shown.bs.modal', function() {
                    const $newPreview = $('#imagePreview');
                    if ($newPreview.length) {
                        $newPreview.attr('src', base64Image);
                        $newPreview.css('display', 'block');
                        $('#imageBase64Input').val(base64Image);
                    } else {
                        console.error('Still no #imagePreview after modal shown');
                    }
                });
            }
        };
    });
</script>
@endsection
