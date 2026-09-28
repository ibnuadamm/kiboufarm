@extends('layouts.app')

@section('title', 'Master Data Persediaan')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-gray-600">Kelola data barang, stok akan bertambah melalui pembelian.</h2>
        <a href="{{ route('persediaan.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition font-medium flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Barang
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                    <th class="px-6 py-4">Nama Barang</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Satuan</th>
                    <th class="px-6 py-4 text-center">Masuk</th>
                    <th class="px-6 py-4 text-center">Keluar</th>
                    <th class="px-6 py-4 text-center text-brand-700 bg-brand-50">Sisa Stok</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($persediaans as $item)
                    <tr class="hover:bg-gray-50 bg-white transition-colors group">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->nama_barang }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $item->satuan }}</td>
                        <td class="px-6 py-4 text-center text-green-600 cursor-help" title="Total Pembelian">{{ $item->stok_masuk }}</td>
                        <td class="px-6 py-4 text-center text-red-600 cursor-help" title="Total Penjualan/Pemakaian">{{ $item->stok_keluar }}</td>
                        <td class="px-6 py-4 text-center font-bold text-gray-900 bg-brand-50/50">{{ $item->stok_sisa }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end">
                                <a href="{{ route('persediaan.edit', $item) }}" class="text-gray-500 hover:text-brand-600 p-1 bg-gray-100 hover:bg-brand-50 rounded">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $persediaans->links() }}
    </div>
@endsection
