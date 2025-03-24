@extends('layouts.app')

@section('title', 'Daftar Aduan')

@section('content')
<div class="card-header shadow mb-4">
    <h1 class="h4 text-white fs-4">Daftar Aduan</h1>
    <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
</div>

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

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th class="p-2 text-dark">Kode Tiket</th>
                <th class="p-2 text-dark">Judul</th>
                <th class="p-2 text-dark">Layanan</th>
                <th class="p-2 text-dark">Unit Asal</th>
                <th class="p-2 text-dark">Unit Saat Ini</th>
                <th class="p-2 text-dark">Status</th>
                <th class="p-2 text-dark">Tanggal Dibuat</th>
                @if (auth()->user()->role_id == 2)
                <th class="p-2 text-dark">Tugaskan PIC</th>
                <th class="p-2 text-dark">Alihkan Unit</th>
                @endif
                <th class="p-2 text-dark">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td class="p-2 text-dark">{{ $ticket->ticket_code }}</td>
                <td class="p-2 text-dark">{{ $ticket->title }}</td>
                <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                <td class="p-2 text-dark">{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                <td class="p-2 text-dark">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                <td class="p-2 text-dark">
                    @if($ticket->status == 0) Pending
                    @elseif($ticket->status == 1) Ditugaskan
                    @else Resolved
                    @endif
                </td>
                <td class="p-2 text-dark">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
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
                <td class="p-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}">
                        Detail
                    </button>
                </td>
            </tr>

            <!-- Modal untuk Percakapan -->
            <div class="modal fade" id="chatModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="chatModalLabel-{{ $ticket->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chatModalLabel-{{ $ticket->id }}">Percakapan untuk {{ $ticket->ticket_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="chat-container" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                                @forelse($ticket->responses as $response)
                                <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: {{ $response->user->role_id == 4 ? 'flex-start' : 'flex-end' }};">
                                    <div class="message-box p-3 rounded shadow-sm"
                                        style="max-width: 70%; background-color: {{ $response->user->role_id == 4 ? '#e9ecef' : ($response->user->role_id == 2 ? '#fff3cd' : '#d1e7dd') }}; border: 1px solid {{ $response->user->role_id == 4 ? '#ced4da' : ($response->user->role_id == 2 ? '#ffc107' : '#28a745') }};">
                                        <p class="text-dark mb-1">
                                            <strong>
                                                @if ($response->user->role_id == 2)
                                                Sistem (Operator)
                                                @else
                                                {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
                                                @endif
                                            </strong> - {{ $response->created_at->format('d-m-Y H:i') }}
                                        </p>
                                        @if ($response->ticket_id_quote)
                                        <span class="fst-italic text-muted small d-block mb-1">
                                            (Membalas: "{{ $response->quotedResponse->message }}")
                                        </span>
                                        @endif
                                        <p class="mb-0">{{ $response->message }}</p>
                                        @forelse($response->uploads as $upload)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $upload->filename_path) }}"
                                                    alt="{{ $upload->filename_ori }}"
                                                    class="img-thumbnail"
                                                    style="width: 128px; height: 128px; object-fit: cover;">
                                            </a>
                                            <p class="small text-muted mt-1">{{ $upload->filename_ori }}</p>
                                        </div>
                                        @empty
                                        <p class="small text-muted mt-1">Tidak ada lampiran gambar.</p>
                                        @endforelse
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                @endforelse

                                @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $ticket->responses->last() && $ticket->responses->last()->user_id != auth()->user()->id && $ticket->responses->last()->user->role_id != 2)
                                <div class="reply-form mt-3" style="display: flex; justify-content: flex-start;">
                                    <form action="{{ route('tickets.reply', $ticket->responses->last()->id) }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->role_id == 2 ? 10 : 8 }}" class="p-2 text-dark text-center">
                    Tidak ada aduan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.transfer-form').each(function() {
            var ticketId = $(this).attr('id').split('-')[1];
            $('#unit_id-' + ticketId).on('change', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: '{{ route("get.services", ":unitId") }}'.replace(':unitId', unitId),
                        method: 'GET',
                        success: function(data) {
                            var $serviceSelect = $('#service_id-' + ticketId);
                            $serviceSelect.empty();
                            $serviceSelect.append('<option value="">Pilih Layanan</option>');
                            $.each(data, function(index, service) {
                                $serviceSelect.append('<option value="' + service.id + '">' + service.svc_name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                        }
                    });
                } else {
                    $('#service_id-' + ticketId).empty().append('<option value="">Pilih Layanan</option>');
                }
            });
        });
    });
</script>
@endsection
@endsection