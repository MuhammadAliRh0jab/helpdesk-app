@extends('layouts.app')

@section('title', 'Aduan Ditugaskan')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Aduan Ditugaskan</h1>

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
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Unit</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Status</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->ticket_code }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->title }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->service->svc_name ?? 'Tidak ditentukan' }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->unit->unit_name ?? 'Tidak ditentukan' }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            @if($ticket->status == 0) Pending
                            @elseif($ticket->status == 1) Ditugaskan
                            @else Resolved
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="py-2 px-4 border-b dark:border-gray-600">
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
                                    </div>
                                @empty
                                    <p class="text-gray-500 dark:text-gray-400">Belum ada percakapan untuk tiket ini.</p>
                                @endforelse
                                @if ($ticket->status != 2)
                                    <form action="{{ route('tickets.respond', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                                        @csrf
                                        <textarea name="message" class="border p-2 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 w-full mb-2" placeholder="Masukkan tanggapan Anda..." required></textarea>
                                        <input type="file" name="images[]" multiple class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 mb-2">
                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                            Kirim Tanggapan
                                        </button>
                                    </form>
                                    @if ($ticket->status == 1)
                                        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="2">
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200 text-center">
                            Tidak ada aduan yang ditugaskan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection