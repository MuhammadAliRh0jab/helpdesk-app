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

    <!-- Main Container -->
    <main id="main-container">
        <!-- Hero -->
        <div>
            <div class="content content-full">
                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
                    <div class="flex-grow-1">
                        <h1 class="h3 fw-bold mb-1">
                            Detail Aduan
                        </h1>
                        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                            Aduan yang telah dikirimkan
                        </h2>
                    </div>
                    <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-alt">
                            <li class="breadcrumb-item">
                                <a class="link-fx" href="javascript:void(0)">Aduan</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                List
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content">
            <!-- Dynamic Table Full -->
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
                                    <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Resolved</span>
                                    @endif

                                <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                                <!-- <td class="p-2 text-dark">{{ $ticket->description }}</td>
                    <td class="p-2 text-dark">{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                    <td class="p-2 text-dark">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                    <td class="p-2 text-dark">
                        @if($ticket->status == 0) Pending
                        @elseif($ticket->status == 1) Ditugaskan
                        @else Resolved
                        @endif
                    </td> -->
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
                                        <button type="submit" class="btn btn-success btn-sm">Tugaskan</button>
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
                                            {{ $pic->username }}
                                            <form action="{{ route('tickets.removePic', $ticket) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="pic_id" value="{{ $pic->pic_id }}">
                                                <button type="submit" class="btn btn-link text-danger text-decoration-none p-0">Hapus</button>
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
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>

                                    <div>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}">
                                            <i class="fas fa-comments"></i>
                                        </button>
                                    </div>
                                </td>

                            </tr>

                            <!-- Modal Detail -->
                            <div class="modal fade" id="detailModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="detailModal-{{ $ticket->id }}" aria-hidden="true" style="font-size: 12px;">
                                <div class=" modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="userDetailModalLabel">Detail Aduan</h5>
                                        </div>
                                        <hr>
                                        <div class="modal-body">
                                            <p><strong>Tanggal Dibuat:</strong> {{ $ticket->created_at }}</p>
                                            <p><strong>Kode Tiket:</strong> {{ $ticket->ticket_code }}</p>
                                            <p><strong>Judul:</strong> {{ $ticket->title }}</p>
                                            <p><strong>Layanan:</strong> {{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</p>
                                            <p><strong>Deskripsi:</strong> {{ $ticket->description }}</p>
                                            <p><strong>Unit Asal:</strong> {{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</p>
                                            <p><strong>Unit Saat Ini:</strong> {{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</p>
                                            <p><strong>Status:</strong>
                                                @if($ticket->status == 0) Pending
                                                @elseif($ticket->status == 1) Ditugaskan
                                                @else Resolved
                                                @endif
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal untuk Percakapan -->
                            <div class="modal fade" id="chatModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="chatModalLabel-{{ $ticket->id }}" aria-hidden="true" style="font-size: 12px;">
                                <div class=" modal-dialog modal-lg">
                                    <div class="modal-content bg-primary-lighter">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="chatModalLabel-{{ $ticket->id }}">Kode Tiket: {{ $ticket->ticket_code }}</h5>
                                        </div>
                                        <hr>
                                        <div class="modal-body">
                                            <div class="chat-container" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                                                @forelse($ticket->responses as $response)
                                                @php
                                                $isSender = $response->user_id == auth()->user()->id;
                                                $bgColor = $isSender ? 'rgb(165, 195, 244)' : 'rgb(236, 236, 236)';
                                                @endphp
                                                <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: {{ $isSender ? 'flex-end' : 'flex-start' }};">
                                                    <p class="text-dark mb-1">
                                                        <strong>
                                                            @if ($response->user->role_id == 2)
                                                            Sistem (Operator)
                                                            @else
                                                            {{ $response->user->username }}
                                                            @endif
                                                        </strong>
                                                    </p>
                                                    @if ($response->ticket_id_quote)
                                                    <span class="fst-italic text-muted small d-block mb-1">
                                                        (Membalas: "{{ $response->quotedResponse->message }}")
                                                    </span>
                                                    @endif
                                                    <div class="message-box p-1 rounded shadow-sm"
                                                        style="max-width: 50%; background-color: {{$bgColor}}">
                                                        @forelse($response->uploads as $upload)
                                                        <div class="mt-2">
                                                            <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                                <img src="{{ asset('storage/' . $upload->filename_path) }}"
                                                                    alt="{{ $upload->filename_ori }}"
                                                                    class="img-thumbnail"
                                                                    style="width: 128px; height: 128px; object-fit: cover;">
                                                            </a>
                                                        </div>
                                                        @empty
                                                        <!-- <p class="small text-muted mt-1">Tidak ada lampiran gambar.</p> -->
                                                        @endforelse
                                                        <p class="mb-0">{{ $response->message }}</p>
                                                        <small class="text-muted d-block mb-1 text-end">
                                                            {{ $response->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                        </small>
                                                    </div>
                                                </div>
                                                @empty
                                                <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                                @endforelse

                                                @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $ticket->responses->last() && $ticket->responses->last()->user_id != auth()->user()->id && $ticket->responses->last()->user->role_id != 2)
                                                <div class="reply-form mt-3" style="display: flex; justify-content: flex-start;">
                                                    <form id="replyForm" action="{{ route('tickets.reply', $ticket->responses->last()->id) }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
                                                        @csrf
                                                        <textarea name="message" class="form-control mb-2" placeholder="Masukkan balasan Anda..." required></textarea>
                                                        <input type="file" name="images[]" multiple class="form-control mb-2">
                                                        <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
                                                    </form>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Tutup</button>
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
</div>
</div>
<script>
    $('#replyForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah form submit default yang menutup modal

        var form = $(this)[0];
        var data = new FormData(form);

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Balasan berhasil dikirim!');
            },
            error: function(xhr) {
                alert('Terjadi kesalahan, coba lagi.');
            }
        });
    });
</script>

@endsection