<div class="modal fade" id="detailModal-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel-{{ $ticket->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title" id="detailModalLabel-{{ $ticket->id }}">Detail Tiket #{{ $ticket->ticket_code }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <p><strong>Judul:</strong> {{ $ticket->title }}</p>
                    <p><strong>Deskripsi:</strong> {{ $ticket->description }}</p>
                    <p><strong>Layanan:</strong> {{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</p>
                    <p><strong>Unit:</strong> {{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</p>
                    @if($ticket->original_unit_id)
                        <p><strong>Unit Asal:</strong> {{ \App\Models\Unit::find($ticket->original_unit_id)->unit_name ?? 'Tidak ditentukan' }}</p>
                    @endif
                    <p><strong>Status:</strong>
                        @if($ticket->status == 0)
                            Pending
                        @elseif($ticket->status == 1)
                            Ditugaskan
                        @else
                            Selesai
                        @endif
                    </p>
                    <p><strong>Pengadu:</strong> {{ $ticket->user->name ?? $ticket->guest_name ?? 'Tidak diketahui' }}</p>
                    <p><strong>Tanggal Dibuat:</strong> {{ $ticket->created_at->timezone('Asia/Jakarta')->format('j F Y, H.i') }}</p>
                    @if($ticket->latitude && $ticket->longitude)
                        <p><strong>Lokasi:</strong> ({{ $ticket->latitude }}, {{ $ticket->longitude }})</p>
                    @endif
                    @if(auth()->user()->role_id == 2)
                        <p><strong>PIC:</strong>
                            @foreach($ticket->pics as $pic)
                                {{ $pic->user->username }} ({{ $pic->pic_desc }})
                                @if($pic->pivot->pic_stats === 'inactive')
                                    <span class="text-danger">[Nonaktif]</span>
                                @endif
                                <br>
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>