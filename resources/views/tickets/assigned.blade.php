@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
<div>
    <h1 class="h4 mb-4 text-dark fs-2">Aduan Ditugaskan</h1>

    @if (session('success'))
    <div class="alert alert-success p-4 mb-4 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger p-4 mb-4 rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th class="p-2 text-dark">Kode Tiket</th>
                    <th class="p-2 text-dark">Judul</th>
                    <th class="p-2 text-dark">Layanan</th>
                    <th class="p-2 text-dark">Unit</th>
                    <th class="p-2 text-dark">Status</th>
                    <th class="p-2 text-dark">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="p-2 text-dark">{{ $ticket->ticket_code }}</td>
                    <td class="p-2 text-dark">{{ $ticket->title }}</td>
                    <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                    <td class="p-2 text-dark">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                    <td class="p-2 text-dark">
                        @if($ticket->status == 0) Pending
                        @elseif($ticket->status == 1) Ditugaskan
                        @else Resolved
                        @endif
                    </td>
                    <td class="p-2 text-dark">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="p-2">
                        <div class="ms-4">
                            <h3 class="h5 fw-semibold text-dark">Percakapan untuk {{ $ticket->ticket_code }}</h3>
                            @forelse($ticket->responses as $response)
                            <div class="border-start border-4 ps-4 mt-2 {{ $response->user->role_id == 4 ? 'border-success' : ($response->user->role_id == 2 ? 'border-warning' : 'border-primary') }}">
                                <p class="text-dark">
                                    <strong>
                                        @if ($response->user->role_id == 2)
                                        Sistem (Operator)
                                        @else
                                        {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
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
                                        <img src="{{ asset('storage/' . $upload->filename_path) }}" alt="{{ $upload->filename_ori }}" class="img-fluid rounded" style="width: 128px; height: 128px; object-fit: cover;">
                                    </a>
                                    <p class="text-muted small">{{ $upload->filename_ori }}</p>
                                </div>
                                @empty
                                <p class="text-muted small">Tidak ada lampiran gambar.</p>
                                @endforelse
                            </div>
                            @empty
                            <p class="text-muted">Belum ada percakapan untuk tiket ini.</p>
                            @endforelse
                            @if ($ticket->status != 2)
                            <form action="{{ route('tickets.respond', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                @csrf
                                <textarea name="message" class="form-control mb-2" placeholder="Masukkan tanggapan Anda..." required></textarea>
                                <input type="file" name="images[]" multiple class="form-control mb-2">
                                <button type="submit" class="btn btn-primary px-2 py-1">
                                    Kirim Tanggapan
                                </button>
                            </form>
                            @if ($ticket->status == 1)
                            <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="btn btn-success px-2 py-1">
                                    Tandai Selesai
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-2 text-dark text-center">
                        Tidak ada aduan yang ditugaskan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection