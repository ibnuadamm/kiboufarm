@extends('layouts.app')

@section('title', 'Master Data Kolam')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Master Data Kolam</h1>
        <a href="{{ route('kolam.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">+ Tambah Kolam Baru</a>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kolam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Ikan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran Ikan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Tebar</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Panen</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-red-500 uppercase tracking-wider">Ikan Mati</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-green-600 uppercase tracking-wider">Jumlah Saat Ini</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kolams as $kolam)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $kolam->nomor_kolam }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $kolam->persediaan->nama_barang ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">{{ $kolam->ukuran_ikan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $kolam->tanggal_tebar ? $kolam->tanggal_tebar->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $kolam->tanggal_panen ? $kolam->tanggal_panen->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-500 font-bold">{{ number_format($kolam->total_mati ?? 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-bold">{{ number_format($kolam->jumlah) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusClass = match($kolam->status) {
                                        'kosong' => 'bg-gray-100 text-gray-800',
                                        'masuk_bibit' => 'bg-blue-100 text-blue-800',
                                        'dalam_pemeliharaan' => 'bg-green-100 text-green-800',
                                        'sortir' => 'bg-yellow-100 text-yellow-800',
                                        'panen' => 'bg-purple-800 text-white',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ str_replace('_', ' ', ucfirst($kolam->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                @if($kolam->status !== 'kosong' && $kolam->status !== 'panen')
                                    <a href="{{ route('kolam.edit', $kolam) }}" class="text-brand-600 hover:text-brand-900 font-medium">Ubah Status</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada kolam.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
