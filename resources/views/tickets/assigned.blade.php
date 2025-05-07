@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
<div class="card mt-4">
    <div class="card-body">
        <h6><strong>Aduan Ditugaskan</strong></h6>
        <p>Aduan yang ditugaskan harus segera diproses dan dibalas segera</p>
        <hr>
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

<div class="table-responsive mb-4 mt-4">
    <table class="table rounded">
        <thead class="table-light">
            <tr class="text-center">
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
            <tr class="text-center">
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
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTicket{{ $ticket->id }}">
                        <i class="fas fa-reply me-1"></i> Balas
                    </button>
                </td>
            </tr>

            <!-- Modal Percakapan Gaya Chat -->
            <div class="modal fade" id="modalTicket{{ $ticket->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $ticket->id }}" aria-hidden="true" style="font-size: 12px;">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $ticket->id }}">Kode Tiket: {{ $ticket->ticket_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
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

                                @if ($ticket->status != 2)
                                <form action="{{ route('tickets.respond', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    <textarea name="message" class="form-control mb-2" placeholder="Masukkan tanggapan Anda..." required></textarea>
                                    <div class="input-group mb-2">
                                        <button class="btn btn-outline-secondary" type="button" id="custom-button-{{ $ticket->id }}">Pilih File</button>
                                        <span class="input-group-text" id="file-name-{{ $ticket->id }}" style="width: 50%;">Tidak ada file dipilih</span>
                                        <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim</button>
                                        @if ($ticket->status == 1)
                                        <button type="submit" form="resolve-form-{{ $ticket->id }}" class="btn btn-success">
                                            <i class="fas fa-check-circle"></i> Tandai Selesai
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($tickets as $ticket)
        document.getElementById('custom-button-{{ $ticket->id }}').addEventListener('click', function() {
            document.getElementById('images-{{ $ticket->id }}').click();
        });

        document.getElementById('images-{{ $ticket->id }}').addEventListener('change', function() {
            const files = this.files;
            const fileNameDisplay = document.getElementById('file-name-{{ $ticket->id }}');
            fileNameDisplay.textContent = files.length > 0 ?
                (files.length > 1 ? `${files.length} file dipilih` : files[0].name) :
                'Tidak ada file dipilih';
        });
        @endforeach
    });
</script>
@endsection