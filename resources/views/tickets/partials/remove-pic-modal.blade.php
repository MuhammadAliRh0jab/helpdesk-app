<div class="modal fade" id="removePicModal-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-labelledby="removePicModalLabel-{{ $ticket->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title" id="removePicModalLabel-{{ $ticket->id }}">Hapus PIC dari Tiket #{{ $ticket->ticket_code }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form action="{{ route('tickets.removePic', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="pic_id" class="form-label">Pilih PIC untuk Dihapus</label>
                            <select name="pic_id" id="pic_id" class="form-control" required>
                                <option value="">Pilih PIC</option>
                                @foreach($ticket->pics as $pic)
                                    @if($pic->pivot->pic_stats === 'active')
                                        <option value="{{ $pic->id }}">{{ $pic->user->username }} - {{ $pic->pic_desc }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>