<div>
    <div class="mb-4 flex items-center">
        <input 
            type="text" 
            wire:model.live="search" 
            class="border rounded px-4 py-2 w-1/2" 
            placeholder="Cari berdasarkan No Ticket...">
    </div>

    <table class="table-auto w-full border-collapse">
        <thead>
            <tr>
                <th class="border px-4 py-2">No</th>
                <th class="border px-4 py-2">No Ticket</th>
                <th class="border px-4 py-2">Judul</th>
                <th class="border px-4 py-2">Kategori</th>
                <th class="border px-4 py-2">Status</th>
                <th class="border px-4 py-2">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($aduans as $index => $aduan)
                <tr>
                    <td class="border px-4 py-2">{{ ($aduans->currentPage() - 1) * $aduans->perPage() + $index + 1 }}</td>
                    <td class="border px-4 py-2">{{ $aduan->ticket_code }}</td>
                    <td class="border px-4 py-2">{{ $aduan->title }}</td>
                    <td class="border px-4 py-2">{{ $aduan->service ? $aduan->service->name : 'N/A' }}</td>
                    <td class="border px-4 py-2">{{ $aduan->status }}</td>
                    <td class="border px-4 py-2">{{ $aduan->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td class="border px-4 py-2 text-center" colspan="6">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $aduans->links() }}
    </div>
</div>