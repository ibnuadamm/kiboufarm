@extends('layouts.app')

@section('title', 'Data Penjualan Ikan')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Penjualan Ikan</h1>
        <a href="{{ route('penjualan.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">
            + Catat Panen Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Panen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kolam (Ikan)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Siklus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah (Ekor)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga/Ekor</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penjualans as $penjualan)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $penjualan->tanggal_penjualan->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-bold">Kolam {{ $penjualan->kolam->nomor_kolam }}</span> 
                                <span class="text-gray-500 text-xs">({{ $penjualan->kolam->persediaan->nama_barang ?? '-' }})</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <div>Tebar: {{ $penjualan->kolam->tanggal_tebar ? $penjualan->kolam->tanggal_tebar->format('d M Y') : '-' }}</div>
                                <div>Awal: {{ number_format($penjualan->kolam->jumlah) }} ekor</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $penjualan->pembeli ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ number_format($penjualan->jumlah_ekor) }} ekor</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">Rp {{ number_format($penjualan->harga_per_ekor) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">Rp {{ number_format($penjualan->total_harga) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $penjualans->links() }}
        </div>
    </div>
@endsection
