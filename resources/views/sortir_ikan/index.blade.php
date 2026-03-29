@extends('layouts.app')

@section('title', 'Riwayat Sortir Ikan')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Sortir Ikan</h1>
        <a href="{{ route('sortir-ikan.create') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">
            + Tambah Sortir
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kolam Asal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kolam Tujuan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Jml Pindah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran Tujuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sortirs as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $s->tanggal->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-bold">Kolam #{{ $s->kolamAsal->nomor_kolam }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-bold">Kolam #{{ $s->kolamTujuan->nomor_kolam }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900 font-bold">{{ number_format($s->jumlah) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $s->ukuran_ikan_tujuan }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $s->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data sortir.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
