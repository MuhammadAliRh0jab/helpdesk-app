@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
<div>
    <div class="card-header shadow">
        <h1 class="h4 text-white fs-4">Aduan Ditugaskan</h1>
        <p class="text-white fs-6">Helpdesk Pemerintah Kota Blitar</p>
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
                                <div class="input-group mb-2">
                                    <button class="btn btn-outline-secondary" type="button" id="custom-button-{{ $ticket->id }}">Pilih File</button>
                                    <span class="input-group-text" id="file-name-{{ $ticket->id }}" style="width: 100%;">Tidak ada file dipilih</span>
                                    <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                </div>
                                <div class="d-flex justify-content-start gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2">
                                        Kirim Tanggapan
                                    </button>
                                    @if ($ticket->status == 1)
                                    <button type="submit" form="resolve-form-{{ $ticket->id }}" class="btn btn-success px-4 py-2">
                                        Tandai Selesai
                                    </button>
                                    @endif
                                    <button type="reset" class="btn btn-dark px-4 py-2">
                                        Batal
                                    </button>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @foreach($tickets as $ticket)
        // Tombol pilih file untuk setiap tiket
        document.getElementById('custom-button-{{ $ticket->id }}').addEventListener('click', function() {
            document.getElementById('images-{{ $ticket->id }}').click();
        });

        // Tampilkan status file setelah dipilih untuk setiap tiket
        document.getElementById('images-{{ $ticket->id }}').addEventListener('change', function() {
            const files = this.files;
            const fileNameDisplay = document.getElementById('file-name-{{ $ticket->id }}');
            if (files.length > 0) {
                fileNameDisplay.textContent = files.length > 1 ?
                    `${files.length} file dipilih` :
                    files[0].name;
            } else {
                fileNameDisplay.textContent = 'Tidak ada file dipilih';
            }
        });
        @endforeach
    });
</script>
@endsection