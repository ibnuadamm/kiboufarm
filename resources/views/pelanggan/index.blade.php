@extends('layouts.app')
@section('title', 'Master Data Pelanggan')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Pelanggan</h1>
        <a href="{{ route('pelanggan.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">+ Tambah Pelanggan</a>
    </div>
    @if(session('success')) <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div> @endif
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($pelanggans as $p)
                <tr>
                    <td class="px-6 py-4">{{ $p->nama }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ $p->tipe }}</span></td>
                    <td class="px-6 py-4">{{ $p->nomor_telepon }}</td>
                    <td class="px-6 py-4">{{ $p->alamat }}</td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('pelanggan.edit', $p) }}" class="text-blue-500 hover:text-blue-700 mr-2">Edit</a>
                        <form action="{{ route('pelanggan.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
