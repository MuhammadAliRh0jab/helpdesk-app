@extends('layouts.app')

@section('title', 'Kelola Layanan')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Kelola Layanan</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border dark:border-gray-700">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Nama Layanan</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Unit</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Kategori</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Status</th>
                    <th class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $service->svc_name }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">{{ $service->unit->unit_name }}</td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            {{ $service->category_id == 1 ? 'Pemerintah' : 'Publik' }}
                        </td>
                        <td class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200">
                            {{ ucfirst($service->status) }}
                        </td>
                        <td class="py-2 px-4 border-b dark:border-gray-600">
                            <form action="{{ route('services.updateStatus', $service) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="border p-1 rounded dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                    <option value="active" {{ $service->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $service->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-2 px-4 border-b dark:border-gray-600 text-gray-800 dark:text-gray-200 text-center">
                            Tidak ada layanan untuk unit Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection