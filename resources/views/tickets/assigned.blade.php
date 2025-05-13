@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">
    <main id="main-container">
        <div class="content">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
                <div class="flex-grow-1 mb-1 mb-md-0">
                    <h1 class="h3 fw-bold mb-2">
                        Aduan Ditugaskan
                    </h1>
                    <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                        Aduan yang ditugaskan harus segera diproses dan dibalas
                    </h2>
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
                    <!-- Default Table -->
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
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTicket{{ $ticket->id }}" title="Balas Pesan">
                                                <i class="fas fa-reply me-1"></i>
                                            </button>
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
                                                    <div class="chat-container" id="chat-container-{{ $ticket->id }}" style="flex: 1; overflow-y: auto; padding: 20px;">
                                                        @forelse($ticket->responses as $response)
                                                        @php
                                                        $isSender = $response->user_id == auth()->user()->id;
                                                        $bgColor = $isSender ? 'rgb(84, 163, 242)' : 'rgb(54, 56, 59)';
                                                        @endphp
                                                        <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: {{ $isSender ? 'flex-end' : 'flex-start' }};">
                                                            <p class="text-dark mb-1">
                                                                <strong>
                                                                    @if ($response->user->role_id == 2)
                                                                    Sistem (Operator)
                                                                    @else
                                                                    {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
                                                                    @endif
                                                                </strong>
                                                            </p>
                                                            @if ($response->ticket_id_quote)
                                                            <span class="fst-italic text-muted small d-block mb-1">
                                                                (Membalas: "{{ $response->quotedResponse->message }}")
                                                            </span>
                                                            @endif
                                                            <div class="message-box p-2 rounded shadow-sm" style="max-width: 60%; background-color: {{ $bgColor }};">
                                                                <p class="mb-1">{{ $response->message }}</p>
                                                                @foreach($response->uploads as $upload)
                                                                <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                                    <img src="{{ asset('storage/' . $upload->filename_path) }}" class="img-thumbnail mt-1" style="width: 128px; height: 128px; object-fit: cover;">
                                                                </a>
                                                                @endforeach
                                                                <small class="text-muted d-block text-end">{{ $response->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}</small>
                                                            </div>
                                                        </div>
                                                        @empty
                                                        <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                                        @endforelse
                                                    </div>

                                                    @if ($ticket->status != 2)
                                                    <div class="form-container" style="position: sticky; bottom: 0; z-index: 10">
                                                        <form id="chat-form-{{ $ticket->id }}" action="{{ route('tickets.respond', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3 gap-2">
                                                                <textarea class="form-control form-control-lg" name="message" placeholder="Masukkan pesan..." required></textarea>
                                                            </div>
                                                            <div class="input-group text-muted mb-2 p-3 gap-2">
                                                                <button class="btn btn-outline-secondary" type="button" id="custom-button-{{ $ticket->id }}">Pilih File</button>
                                                                <span class="input-group-text" id="file-name-{{ $ticket->id }}" style="width: 75%;">Tidak ada file dipilih</span>
                                                                <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                                                <button type="submit" class="btn p-0 text-primary" title="Kirim Pesan">
                                                                    <i class="fas fa-paper-plane fs-5"></i>
                                                                </button>
                                                                @if ($ticket->status == 1)
                                                                <button type="submit" form="resolve-form-{{ $ticket->id }}" class="btn p-0 text-success ms-2 border-0 bg-transparent" title="Tandai Selesai">
                                                                    <i class="fas fa-check-circle fs-5"></i>
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
@endsection

<style>
    .modal-content {
        position: relative;
        z-index: 1;
    }

    .modal-content::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('/assets/media/img/bg-chat.jpg');
        background-size: cover;
        background-position: center;
        opacity: 0.03;
        z-index: 0;
    }

    .modal-content>* {
        position: relative;
        z-index: 1;
    }

    @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');

    .modal-content {
        font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .message-box p {
        font-size: 14px;
        color: #fff;
    }
</style>