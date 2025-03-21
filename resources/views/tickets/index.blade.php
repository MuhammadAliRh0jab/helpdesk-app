@extends('layouts.app')

@section('title', 'Daftar Aduan')

@section('content')
<h1 class="mb-4 text-dark fs-2">Daftar Aduan</h1>

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
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td class="p-2 text-dark">{{ $ticket->ticket_code }}</td>
                <td class="p-2 text-dark">{{ $ticket->title }}</td>
                <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                <td class="p-2 text-dark">{{ $ticket->original_unit_id ?
                    \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak
                    ditentukan') }}</td>
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
                        <button type="submit" class="btn btn-success btn-sm">
                            Tugaskan
                        </button>
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
                                <button type="submit"
                                    class="btn btn-link text-danger text-decoration-none p-0">Hapus</button>
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
                    <form action="{{ route('tickets.transfer', $ticket) }}" method="POST"
                        id="transferForm-{{ $ticket->id }}" class="transfer-form">
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
                        <button type="submit" class="btn btn-warning btn-sm">
                            Alihkan
                        </button>
                    </form>
                    @else
                    <span class="text-muted">
                        Tidak dapat dialihkan
                    </span>
                    @endif
                </td>
                @endif
            </tr>
            <tr>
                <td colspan="{{ auth()->user()->role_id == 2 ? 9 : 7 }}" class="p-2">
                    <div class="ms-3">
                        <h3 class="h5 fw-bold text-dark">Percakapan untuk {{ $ticket->ticket_code }}</h3>
                        @forelse($ticket->responses as $response)
                        <div
                            class="border-start border-4 ps-3 mt-2 {{ $response->user->role_id == 4 ? 'border-success' : ($response->user->role_id == 2 ? 'border-warning' : 'border-primary') }}">
                            <p class="text-dark">
                                <strong>
                                    @if ($response->user->role_id == 2)
                                    Sistem (Operator)
                                    @else
                                    {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' :
                                    'PIC' }})
                                    @endif
                                    - {{ $response->created_at->format('d-m-Y H:i') }}:
                                </strong>
                                @if ($response->ticket_id_quote)
                                <span class="fst-italic text-muted">
                                    (Membalas: "{{ $response->quotedResponse->message }}")
                                </span>
                                @endif
                                <br>
                                {{ $response->message }}
                            </p>
                            @forelse($response->uploads as $upload)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $upload->filename_path) }}"
                                        alt="{{ $upload->filename_ori }}" class="img-thumbnail"
                                        style="width: 128px; height: 128px; object-fit: cover;">
                                </a>
                                <p class="small text-muted">{{ $upload->filename_ori }}</p>
                            </div>
                            @empty
                            <p class="small text-muted">Tidak ada lampiran gambar.</p>
                            @endforelse
                            @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id &&
                            $ticket->status != 2 && $response === $ticket->responses->last() && $response->user_id !=
                            auth()->user()->id && $response->user->role_id != 2)
                            <form action="{{ route('tickets.reply', $response->id) }}" method="POST"
                                enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <textarea name="message" class="form-control mb-2"
                                    placeholder="Masukkan balasan Anda..." required></textarea>
                                <input type="file" name="images[]" multiple class="form-control mb-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    Kirim Balasan
                                </button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted">Belum ada percakapan untuk tiket ini.</p>
                        @endforelse
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ auth()->user()->role_id == 2 ? 9 : 7 }}" class="p-2 text-dark text-center">
                    Tidak ada aduan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.transfer-form').each(function() {
            var ticketId = $(this).attr('id').split('-')[1];
            $('#unit_id-' + ticketId).on('change', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: '{{ route('get.services',' : unitId ') }}'.replace(':unitId', unitId),
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
