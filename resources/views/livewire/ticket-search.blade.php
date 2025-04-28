<div>
    <!-- Search Bar -->
    <div class="mt-3 mb-4">
        <div class="input-group">
            <span class="input-group-text bg-primary">
                <i class="bi bi-search text-white"></i>
            </span>
            <input 
                type="text" 
                wire:model.live="search" 
                class="form-control" 
                placeholder="Cari berdasarkan No Tiket atau Judul...">
        </div>
    </div>
    
    <!-- Table -->
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
                @forelse ($tickets as $index => $ticket)
                    <tr class="text-nowrap">
                        <th>{{ ($tickets->currentPage() - 1) * $tickets->perPage() + $index + 1 }}</th>
                        <td>{{ $ticket->ticket_code }}</td>
                        <td>{{ $ticket->title }}</td>
                        <td>{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                        <td>{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                        <td>
                            @if($ticket->status == 0) 
                                <span class="badge bg-warning">Belum Direspon</span>
                            @elseif($ticket->status == 1) 
                                <span class="badge bg-info">Direspon</span>
                            @else 
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td>{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <a class="btn m-1 rounded-pill btn-primary" href="#">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="alert alert-info m-0">
                                        Tidak ada tiket yang ditemukan. Silahkan buat aduan baru atau coba kata kunci pencarian lain.
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $tickets->links() }}
    </div>
</div>