<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aduan - Helpdesk Kota</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 p-6">
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

    @if ($canCreateTicket)
        <div class="mb-4">
            <a href="{{ route('tickets.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                Buat Aduan Baru
            </a>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border dark:border-gray-700">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Kode Tiket</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Judul</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Status</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Tanggal Dibuat</th>
                    @if (auth()->user()->role_id == 2)
                        <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Tugaskan PIC</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->ticket_code }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->title }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            @if($ticket->status == 0) Pending
                            @elseif($ticket->status == 1) Ditugaskan
                            @else Resolved
                            @endif
                        </td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $ticket->created_at->format('d-m-Y H:i') }}</td>
                        @if (auth()->user()->role_id == 2)
                            <td class="py-2 px-4 border-b dark:border-gray-600">
                                @if ($ticket->status == 0)
                                    @if ($pics->isNotEmpty())
                                        <form action="{{ route('tickets.assign', $ticket) }}" method="POST">
                                            @csrf
                                            <select name="pic_id" class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                                <option value="">Pilih PIC</option>
                                                @foreach ($pics as $pic)
                                                    <option value="{{ $pic->id }}">{{ $pic->username }} ({{ $pic->pic_desc }})</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                                                Tugaskan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-red-500 dark:text-red-400">Tidak ada PIC tersedia untuk unit ini.</span>
                                    @endif
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Sudah ditugaskan
                                    </span>
                                @endif
                            </td>
                        @endif
                    </tr>
                    <!-- Bagian untuk menampilkan riwayat percakapan -->
                    <tr>
                        <td colspan="{{ auth()->user()->role_id == 2 ? 5 : 4 }}" class="py-2 px-4 border-b dark:border-gray-600">
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Percakapan untuk {{ $ticket->ticket_code }}</h3>
                                @forelse($ticket->responses as $response)
                                    <div class="border-l-4 pl-4 mt-2 {{ $response->user->role_id == 4 ? 'border-green-500' : 'border-blue-500' }}">
                                        <p class="text-gray-700 dark:text-gray-300">
                                            <strong>{{ $response->user->username }} ({{ $response->user->role_id == 4 ? 'Pengadu' : 'PIC' }}) - {{ $response->created_at->format('d-m-Y H:i') }}:</strong>
                                            @if ($response->ticket_id_quote)
                                                <span class="italic text-gray-500 dark:text-gray-400">
                                                    (Membalas: "{{ $response->quotedResponse->message }}")
                                                </span>
                                            @endif
                                            <br>
                                            {{ $response->message }}
                                        </p>
                                        <!-- Tampilkan gambar yang dilampirkan -->
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
                                        <!-- Form untuk pengadu membalas, hanya untuk respons terakhir -->
                                        @if (auth()->user()->role_id == 4 && $ticket->user_id == auth()->user()->id && $ticket->status != 2 && $response === $ticket->responses->last() && $response->user_id != auth()->user()->id)
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
                        <td colspan="{{ auth()->user()->role_id == 2 ? 5 : 4 }}" class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200 text-center">
                            Tidak ada aduan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('logout') }}" class="inline-block bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</body>
</html>