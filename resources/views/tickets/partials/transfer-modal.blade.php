<div class="modal fade" id="transferModal-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel-{{ $ticket->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title" id="transferModalLabel-{{ $ticket->id }}">Alihkan Unit untuk Tiket #{{ $ticket->ticket_code }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form action="{{ route('tickets.transfer', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Pilih Unit</label>
                            <select name="unit_id" id="unit_id" class="form-control" required>
                                <option value="">Pilih Unit</option>
                                @foreach(\App\Models\Unit::all() as $unit)
                                    @if($unit->id != $ticket->unit_id)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="service_id" class="form-label">Pilih Layanan</label>
                            <select name="service_id" id="service_id" class="form-control" required>
                                <option value="">Pilih Layanan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Alihkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>