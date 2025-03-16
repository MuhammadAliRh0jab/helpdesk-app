@extends('layouts.app')

@section('title', 'Daftar Aduan')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Daftar Aduan</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border dark:border-gray-700">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Kode Tiket</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Judul</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Layanan</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Unit Asal</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Unit Saat Ini</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Status</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Tanggal Dibuat</th>
                    @if (auth()->user()->role_id == 2)
                        <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Tugaskan PIC</th>
                        <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Alihkan Unit</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->ticket_code }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->title }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->original_unit_id ? \App\Models\Unit::find($ticket->original_unit_id)->unit_name : ($ticket->unit->unit_name ?? 'Tidak ditentukan') }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            @if($ticket->status == 0) Pending
                            @elseif($ticket->status == 1) Ditugaskan
                            @else Resolved
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                        @if (auth()->user()->role_id == 2)
                            <td class="py-2 px-4 border-b dark:border-gray-600">
                                @if ($ticket->status != 2) <!-- Hanya jika tiket belum resolved -->
                                    @if ($pics->isNotEmpty())
                                        <!-- Form untuk menugaskan PIC baru -->
                                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST" class="mb-2">
                                            @csrf
                                            <select name="pic_id" class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                                <option value="">Pilih PIC</option>
                                                @foreach ($pics as $pic)
                                                    <option value="{{ $pic->id }}">{{ $pic->username }} ({{ $pic->pic_desc }})</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700 ml-2">
                                                Tugaskan
                                            </button>
                                        </form>

                                        <!-- Daftar PIC yang ditugaskan -->
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
                                            <ul class="list-disc ml-4 text-gray-700 dark:text-gray-300">
                                                @foreach($activePics as $pic)
                                                    <li>
                                                        {{ $pic->username }}
                                                        <form action="{{ route('tickets.removePic', $ticket) }}" method="POST" class="inline ml-2">
                                                            @csrf
                                                            <input type="hidden" name="pic_id" value="{{ $pic->pic_id }}">
                                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
                                                        </form>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Belum ada PIC ditugaskan.</span>
                                        @endif
                                    @else
                                        <span class="text-red-500 dark:text-red-400">Tidak ada PIC tersedia untuk unit ini.</span>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Tiket sudah resolved</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border-b dark:border-gray-600">
                                @if ($ticket->status == 0)
                                    <form action="{{ route('tickets.transfer', $ticket) }}" method="POST" id="transferForm-{{ $ticket->id }}" class="transfer-form">
                                        @csrf
                                        <select name="unit_id" id="unit_id-{{ $ticket->id }}" class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                                            <option value="">Pilih Unit</option>
                                            @foreach (\App\Models\Unit::all() as $unit)
                                                @if ($unit->id != $ticket->unit_id)
                                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <select name="service_id" id="service_id-{{ $ticket->id }}" class="border p-1 rounded mt-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200" required>
                                            <option value="">Pilih Layanan</option>
                                        </select>
                                        <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded mt-2 hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-700">
                                            Alihkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Tidak dapat dialihkan
                                    </span>
                                @endif
                            </td>
                        @endif
                    </tr>
                    <!-- Bagian untuk menampilkan riwayat percakapan -->
                    <tr>
                        <td colspan="{{ auth()->user()->role_id == 2 ? 9 : 7 }}" class="py-2 px-4 border-b dark:border-gray-600">
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Percakapan untuk {{ $ticket->ticket_code }}</h3>
                                @forelse($ticket->responses as $response)
                                    <div class="border-l-4 pl-4 mt-2 {{ $response->user->role_id == 4 ? 'border-green-500' : ($response->user->role_id == 2 ? 'border-yellow-500' : 'border-blue-500') }}">
                                        <p class="text-gray-700 dark:text-gray-300">
                                            <strong>
                                                @if ($response->user->role_id == 2)
                                                    Sistem (Operator)
                                                @else
                                                    {{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }})
                                                @endif
                                                - {{ $response->created_at->format('d-m-Y H:i') }}:
                                            </strong>
                                            @if ($response->ticket_id_quote)
                                                <span class="italic text-gray-500 dark:text-gray-400">
                                                    (Membalas: "{{ $response->quotedResponse->message }}")
                                                </span>
                                            @endif
                                            <br>
                                            {{ $response->message }}
                                        </p>
                                        @forelse($response->uploads as $upload)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $upload->filename_path) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $upload->filename_path) }}" alt="{{ $upload->filename_ori }}" class="w-32 h-32 object-cover rounded">
                                                </a>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $upload->filename_ori }}</p>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada lampiran gambar.</p>
                                        @endforelse
                                        @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $response === $ticket->responses->last() && $response->user_id != auth()->user()->id && $response->user->role_id != 2)
                                            <form action="{{ route('tickets.reply', $response->id) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                                                @csrf
                                                <textarea name="message" class="border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 w-full mb-2" placeholder="Masukkan balasan Anda..." required></textarea>
                                                <input type="file" name="images[]" multiple class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 mb-2">
                                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                                    Kirim Balasan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">Belum ada percakapan untuk tiket ini.</p>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->role_id == 2 ? 9 : 7 }}" class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200 text-center">
                            Tidak ada aduan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.transfer-form').each(function() {
                    var ticketId = $(this).attr('id').split('-')[1];
                    $('#unit_id-' + ticketId).on('change', function() {
                        var unitId = $(this).val();
                        if (unitId) {
                            $.ajax({
                                url: '{{ route('get.services', ':unitId') }}'.replace(':unitId', unitId),
                                method: 'GET',
                                success: function(data) {
                                    var $serviceSelect = $('#service_id-' + ticketId);
                                    $serviceSelect.empty();
                                    $serviceSelect.append('<option value="">Pilih Layanan</option>');
                                    $.each(data, function(index, service) {
                                        $serviceSelect.append('<option value="' + service.id + '">' + service.svc_name + '</option>');
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.log('Error:', error);
                                }
                            });
                        } else {
                            $('#service_id-' + ticketId).empty().append('<option value="">Pilih Layanan</option>');
                        }
                    });
                });
            });
        </script>
    @endsection
@endsection