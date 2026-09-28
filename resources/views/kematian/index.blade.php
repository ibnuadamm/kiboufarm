@extends('layouts.app')
@section('title', 'Data Kematian Ikan')
@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Kematian</h1>
        <a href="{{ route('kematian.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">+ Catat Kematian</a>
    </div>
    @if(session('success')) <div class="mb-4 bg-green-100 text-green-700 p-3 rounded">{{ session('success') }}</div> @endif
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Kolam</th>
                    <th class="px-6 py-3 text-right">Jumlah (Ekor)</th>
                    <th class="px-6 py-3 text-left">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($kematians as $k)
                <tr>
                    <td class="px-6 py-4">{{ $k->tanggal->format('d M Y') }}</td>
                    <td class="px-6 py-4 font-bold">Kolam {{ $k->kolam->nomor_kolam }}</td>
                    <td class="px-6 py-4 text-right text-red-600 font-bold">{{ number_format($k->jumlah_ekor) }}</td>
                    <td class="px-6 py-4">{{ $k->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $kematians->links() }}</div>
    </div>
@endsection
