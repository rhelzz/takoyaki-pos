@extends('layouts.app')

@section('title', 'Detail Stock Masuk - Takoyaki POS')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="px-4 py-3">
            <div class="flex items-center space-x-3">
                <a href="{{ route('stock-masuk.index') }}" class="p-2 text-gray-600 hover:text-gray-800 -ml-2">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Detail Stock Masuk</h1>
                    <p class="text-xs text-gray-500">{{ $stockMasuk->judul }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-4 max-w-3xl mx-auto space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-xl font-bold mb-2 text-gray-800">{{ $stockMasuk->judul }}</h2>
            <div class="text-sm text-gray-600 mb-2">{{ $stockMasuk->tanggal->format('d/m/Y') }}</div>
            @if($stockMasuk->deskripsi)
                <div class="mb-3 text-gray-700">{{ $stockMasuk->deskripsi }}</div>
            @endif

            <div class="mb-4">
                <h4 class="font-semibold text-gray-700 mt-2 mb-2 flex items-center">
                    <i class="fas fa-fish text-blue-600 mr-2"></i>
                    Topping
                </h4>
                @if(count($stockMasuk->toppings ?? []) > 0)
                    <div class="bg-blue-50 rounded-lg p-3">
                        <table class="w-full">
                            <tbody>
                                @foreach($stockMasuk->toppings as $topping)
                                <tr class="border-b border-blue-100 last:border-0">
                                    <td class="py-2 text-gray-700">{{ $topping['name'] }}</td>
                                    <td class="py-2 text-right font-semibold text-blue-700">
                                        {{ $topping['qty'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada data topping</p>
                @endif
                
                <h4 class="font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                    <i class="fas fa-box text-orange-600 mr-2"></i>
                    Packaging
                </h4>
                @if(count($stockMasuk->packagings ?? []) > 0)
                    <div class="bg-orange-50 rounded-lg p-3">
                        <table class="w-full">
                            <tbody>
                                @foreach($stockMasuk->packagings as $packaging)
                                <tr class="border-b border-orange-100 last:border-0">
                                    <td class="py-2 text-gray-700">{{ $packaging['name'] }}</td>
                                    <td class="py-2 text-right font-semibold text-orange-700">
                                        {{ $packaging['qty'] }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada data packaging</p>
                @endif
            </div>
        </div>
        <div class="text-center">
            <a href="{{ route('stock-masuk.index') }}"
                class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
            </a>
        </div>
    </div>
</div>
@endsection