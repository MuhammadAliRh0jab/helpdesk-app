<div class="modal fade" id="chatModal-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel-{{ $ticket->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-popout" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title" id="chatModalLabel-{{ $ticket->id }}">Pesan Tiket #{{ $ticket->ticket_code }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <div class="chat-messages" style="max-height: 300px; overflow-y: auto;">
                        @foreach($ticket->responses as $response)
                            <div class="mb-2 {{ $response->user_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                <strong>{{ $response->user->username }}:</strong>
                                <p>{{ $response->message }}</p>
                                <small>{{ $response->created_at->timezone('Asia/Jakarta')->format('j F Y, H.i') }}</small>
                                @if($response->ticket_id_quote)
                                    @php
                                        $quotedResponse = $ticket->responses->where('id', $response->ticket_id_quote)->first();
                                    @endphp
                                    @if($quotedResponse)
                                        <blockquote class="blockquote">
                                            <p class="mb-0">{{ $quotedResponse->message }}</p>
                                            <footer class="blockquote-footer">{{ $quotedResponse->user->username }} - {{ $quotedResponse->created_at->timezone('Asia/Jakarta')->format('j F Y, H.i') }}</footer>
                                        </blockquote>
                                    @endif
                                @endif
                                @foreach($response->uploads as $upload)
                                    <div>
                                        <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">Lampiran: {{ $upload->filename_ori }}</a>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    @if(auth()->user()->role_id == 4)
                        <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" class="mt-3 chat-form" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" class="form-control" rows="2" placeholder="Ketik pesan..." required></textarea>
                                <input type="file" name="images[]" class="form-control" multiple>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    @elseif(auth()->user()->role_id == 3)
                        <form action="{{ route('tickets.respond', $ticket->id) }}" method="POST" class="mt-3 chat-form" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <textarea name="message" class="form-control" rows="2" placeholder="Ketik pesan..." required></textarea>
                                <input type="file" name="images[]" class="form-control" multiple>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>