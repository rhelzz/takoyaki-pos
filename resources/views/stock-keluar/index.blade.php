@extends('layouts.app')

@section('title', 'Stock Keluar - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 px-4 py-4 sticky top-0 z-10">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Stock Keluar</h1>
                <p class="text-sm text-gray-600 hidden sm:block">Laporan stock keluar per cabang & tanggal</p>
            </div>
            <a href="{{ route('stock-keluar.create') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm">
                <i class="fas fa-plus mr-1"></i>
                <span class="hidden sm:inline">Tambah</span>
            </a>
        </div>
    </div>
    <div class="p-4 max-w-6xl mx-auto space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Desktop Table --}}
            <div class="hidden sm:block">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockKeluar as $row)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $row->tanggal->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold">{{ $row->judul }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ Str::limit($row->deskripsi, 40) }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('stock-keluar.show', $row) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm mr-2">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                <a href="{{ route('stock-keluar.edit', $row) }}"
                                   class="text-yellow-600 hover:text-yellow-800 text-sm mr-2">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ route('stock-keluar.destroy', $row) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                            onclick="return confirm('Hapus data ini?')">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-gray-500">Belum ada data stock keluar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Mobile Card List --}}
            <div class="sm:hidden divide-y divide-gray-100">
                @forelse($stockKeluar as $row)
                    <div class="py-3 px-4 flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-xs text-gray-400">{{ $row->tanggal->format('d/m/Y') }}</div>
                                <div class="font-bold text-orange-700">{{ $row->judul }}</div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('stock-keluar.show', $row) }}" class="text-blue-500 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('stock-keluar.edit', $row) }}" class="text-yellow-500 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock-keluar.destroy', $row) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 p-1" onclick="return confirm('Hapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="text-xs text-gray-600 truncate">{{ $row->deskripsi }}</div>
                    </div>
                @empty
                    <div class="py-10 text-center text-gray-500">Belum ada data stock keluar</div>
                @endforelse
            </div>
            @if ($stockKeluar instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-4 border-t border-gray-200 bg-gray-50">
                {{ $stockKeluar->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection