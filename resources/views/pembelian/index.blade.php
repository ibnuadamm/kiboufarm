@extends('layouts.app')

@section('title', 'Data Pembelian Pakan dan Bibit')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-gray-600">Catat pembelian untuk menambah stok barang.</h2>
        <a href="{{ route('pembelian.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Catat Pembelian
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Produk</th>
                    <th class="px-6 py-4 text-center">Jumlah</th>
                    <th class="px-6 py-4 text-right">Harga Satuan</th>
                    <th class="px-6 py-4 text-right">Total Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pembelians as $beli)
                    <tr class="hover:bg-gray-50 bg-white transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                            {{ $beli->tanggal_pembelian->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $beli->persediaan->nama_barang }}
                            <div class="text-xs text-gray-500">{{ $beli->persediaan->kategori }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-gray-900">{{ $beli->jumlah }}</span>
                            <span class="text-xs text-gray-500">{{ $beli->persediaan->satuan }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600">
                            Rp {{ number_format($beli->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            Rp {{ number_format($beli->total_harga, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada data pembelian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pembelians->links() }}
    </div>
@endsection
