<!-- Form Pencarian -->
<form action="{{ route('tickets.index') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari tiket..."
            value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Cari</button>
    </div>
</form>

<!-- Tabel Tiket -->
<table class="table table-bordered table-striped table-vcenter js-dataTable-full">
    <thead>
        <tr>
            <th class="text-center" style="width: 80px;">#</th>
            <th>Tanggal Dibuat</th>
            <th class="d-none d-sm-table-cell">Kode Tiket</th>
            <th class="d-none d-sm-table-cell">Judul</th>
            <th class="d-none d-sm-table-cell">Status</th>
            <th class="d-none d-sm-table-cell">Layanan</th>
            @if (auth()->user()->role_id == 2)
                <th class="d-none d-sm-table-cell">Tugaskan PIC</th>
                <th class="d-none d-sm-table-cell">Alihkan Unit</th>
            @endif
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
                        <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                    @endif
                </td>
                <td class="p-2 text-dark">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
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
                                    <button type="submit" class="btn btn-success btn-sm" title="Tugaskan PIC"><i class="far fa-user"></i></button>
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
                                                <p style="font-weight: 600; font-size: 14px; color: #333; background: #f1f3f5; padding: 4px 8px; border-radius: 4px; display: inline-block; margin: 0;">{{ $pic->username }}</p>
                                                <form action="{{ route('tickets.removePic', $ticket) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="pic_id" value="{{ $pic->pic_id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus PIC">
                                                        <i class="fa fa-fw fa-trash-can"></i>
                                                    </button>
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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal-{{ $ticket->id }}" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#chatModal-{{ $ticket->id }}" title="Pesan">
                            <i class="fas fa-comments"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <!-- Modal Detail -->
            <div class="modal fade bg-dark" id="detailModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="detailModalLabel-{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true" style="font-size: 12px;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel-{{ $ticket->id }}">Detail Tiket: {{ $ticket->ticket_code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Waktu Dibuat:</strong> {{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H:i') }}</p>
                            <p><strong>Kode Tiket:</strong> {{ $ticket->ticket_code }}</p>
                            <p><strong>Judul:</strong> {{ $ticket->title }}</p>
                            <p><strong>Status:</strong>
                                @if($ticket->status == 0)
                                    <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-warning-light text-warning">Pending</span>
                                @elseif($ticket->status == 1)
                                    <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-info-light text-info">Ditugaskan</span>
                                @else
                                    <span class="fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill bg-success-light text-success">Selesai</span>
                                @endif
                            </p>
                            <p><strong>Layanan:</strong> {{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</p>
                            <p><strong>Deskripsi:</strong> {{ $ticket->description }}</p>
                            <p><strong>Unit Asal:</strong> {{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</p>
                            <p><strong>Unit Saat Ini:</strong> {{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</p>
                            <div class="mt-3">
                                <strong>Lokasi Aduan:</strong>
                                @if ($ticket->latitude && $ticket->longitude)
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        <div class="d-flex gap-3">
                                            <p class="mb-0"><strong>Latitude:</strong> {{ $ticket->latitude }}</p>
                                            <p class="mb-0"><strong>Longitude:</strong> {{ $ticket->longitude }}</p>
                                        </div>
                                        <div id="map-{{ $ticket->id }}" style="height: 200px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </div>
                                @else
                                    <p class="text-muted">Lokasi tidak tersedia.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal untuk Percakapan -->
            <div class="modal fade bg-dark" id="chatModal-{{ $ticket->id }}" tabindex="-1" aria-labelledby="chatModalLabel-{{ $ticket->id }}" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="modal-header d-flex justify-content-between align-items-center py-3 px-4" style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
                            <h5 class="modal-title d-flex align-items-center gap-2 m-0" id="chatModalLabel-{{ $ticket->id }}">
                                <i class="fas fa-ticket-alt" style="color: #2563eb;"></i>
                                <span style="font-weight: 600; font-size: 1rem; color: #1f2937;">{{ $ticket->ticket_code }}</span>
                                @if($ticket->status == 0)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Pending</span>
                                @elseif($ticket->status == 1)
                                    <span class="badge bg-info text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Ditugaskan</span>
                                @else
                                    <span class="badge bg-success text-white" style="font-size: 0.75rem; border-radius: 12px; padding: 0.25rem 0.75rem;">Selesai</span>
                                @endif
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0" style="display: flex; flex-direction: column; max-height: 70vh;">
                            <div class="chat-container" id="chat-container-{{ $ticket->id }}" style="flex: 1; overflow-y: auto; padding: 1.25rem; background-color: #f9fafb;">
                                @forelse($ticket->responses as $response)
                                    @php
                                        $isSender = $response->user_id == auth()->user()->id;
                                    @endphp
                                    <div class="message-wrapper mb-3" style="display: flex; flex-direction: column; align-items: {{ $isSender ? 'flex-end' : 'flex-start' }};">
                                        <div class="message-info d-flex align-items-center gap-2 mb-1">
                                            @if (!$isSender)
                                                <div class="avatar" style="width: 28px; height: 28px; border-radius: 50%; background-color: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;">
                                                    {{ substr($response->user->username, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="message-sender" style="font-weight: 600; font-size: 0.85rem; color: #374151;">
                                                @if ($response->user->role_id == 2)
                                                    Sistem (Operator)
                                                @else
                                                    {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
                                                @endif
                                            </span>
                                        </div>
                                        @if ($response->ticket_id_quote)
                                            <div class="message-quote" style="font-style: italic; {{ $isSender ? 'color: rgba(255, 255, 255, 0.8);' : 'color: #6b7280;' }} font-size: 0.8rem; margin-bottom: 6px; padding-left: 8px; border-left: 2px solid {{ $isSender ? 'rgba(255, 255, 255, 0.5)' : '#d1d5db' }};">
                                                "{{ $response->quotedResponse->message }}"
                                            </div>
                                        @endif
                                        <div class="message-box {{ $isSender ? 'sent' : 'received' }}" style="max-width: 80%; padding: 12px 16px; border-radius: 16px; margin-bottom: 0.5rem; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                                            @forelse($response->uploads as $upload)
                                                <div class="message-attachment mb-2">
                                                    <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $upload->filename_path) }}" alt="{{ $upload->filename_ori }}" style="width: 128px; height: 128px; object-fit: cover; border-radius: 8px; {{ !$isSender ? 'border: 1px solid #e5e7eb;' : '' }}">
                                                    </a>
                                                </div>
                                            @empty
                                            @endforelse
                                            <p class="mb-0" style="line-height: 1.5; font-size: 0.9rem;">{{ $response->message }}</p>
                                            <span class="message-time" style="font-size: 0.7rem; {{ $isSender ? 'color: rgba(255, 255, 255, 0.85);' : 'color: #6b7280;' }} display: block; text-align: right; margin-top: 4px;">
                                                {{ $response->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">Belum ada percakapan untuk tiket ini.</p>
                                @endforelse
                            </div>

                            @php
                                $hasPegawaiResponse = $ticket->responses()
                                    ->where('user_id', '!=', auth()->user()->id)
                                    ->whereHas('user', function ($query) {
                                        $query->where('role_id', 3);
                                    })
                                    ->exists();

                                $pengaduMessagesSinceLastPegawai = $ticket->responses()->where('user_id', auth()->user()->id)->count();
                                if ($hasPegawaiResponse) {
                                    $lastPegawaiResponse = $ticket->responses()
                                        ->where('user_id', '!=', auth()->user()->id)
                                        ->whereHas('user', function ($query) {
                                            $query->where('role_id', 3);
                                        })
                                        ->latest()
                                        ->first();
                                    $pengaduMessagesSinceLastPegawai = $ticket->responses()
                                        ->where('user_id', auth()->user()->id)
                                        ->where('created_at', '>', $lastPegawaiResponse->created_at)
                                        ->count();
                                }
                            @endphp

                            @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $pengaduMessagesSinceLastPegawai < 10)
                                <div class="reply-container" style="background-color: white; border-top: 1px solid #e5e7eb; padding: 1rem;">
                                    <form id="reply-form-{{ $ticket->id }}" action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="reply-form">
                                        @csrf
                                        <div class="reply-input-row d-flex gap-2 mb-2">
                                            <textarea class="form-control" name="message" placeholder="Ketik pesan Anda di sini..." required style="border-radius: 24px; border-color: #d1d5db; padding: 12px 16px; font-size: 0.9rem; resize: none;"></textarea>
                                            <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="border-radius: 50%; width: 44px; height: 44px; padding: 0;">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                        <div class="attachment-row d-flex align-items-center gap-2">
                                            <button class="btn btn-outline-primary" type="button" id="custom-button-{{ $ticket->id }}" style="border-radius: 20px; padding: 6px 14px; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                <i class="fas fa-paperclip"></i>
                                                <span>Lampirkan File</span>
                                            </button>
                                            <span id="file-name-{{ $ticket->id }}" class="text-muted" style="font-size: 0.85rem; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tidak ada file dipilih</span>
                                            <input type="file" name="images[]" id="images-{{ $ticket->id }}" multiple class="form-control d-none">
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <tr>
                <td colspan="{{ auth()->user()->role_id == 2 ? 9 : 7 }}" class="p-2 text-dark text-center">
                    Tidak ada aduan yang ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
@if ($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-center">
        {{ $tickets->withQueryString()->links() }}
    </div>
@endif