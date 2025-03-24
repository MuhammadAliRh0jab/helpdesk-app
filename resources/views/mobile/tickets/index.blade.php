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
            <div class="modal-body" id="modal-responses-body" style="max-height: 400px; overflow-y: auto;">
                <!-- Daftar respon akan ditampilkan di sini -->
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
<script>
    $(document).ready(function() {
        // Modal Detail Tiket
        $('.show-ticket-detail').on('click', function() {
            const ticket = $(this).data('ticket');

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

            $('#ticketDetailModal').modal('show');
        });

        // Modal Respon Tiket (Chat Bubble Style)
        $('.show-ticket-responses').on('click', function() {
            const ticket = $(this).data('ticket');
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
                    } else {
                        responseHtml += ``;
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
                                <textarea name="message" class="form-control mb-2" placeholder="Masukkan balasan Anda..." required></textarea>
                                <input type="file" name="images[]" multiple class="form-control mb-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Kirim Balasan</button>
                            </form>
                        `);
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
            }

            // Tampilkan modal
            $('#ticketResponsesModal').modal('show');
        });

        // Handle submit form balasan via AJAX
        $(document).on('submit', '#replyForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
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
                    alert('Gagal mengirim balasan: ' + (xhr.responseJSON.message || 'Unknown error'));
                }
            });
        });
    });
</script>
@endsection
