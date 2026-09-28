@extends('layouts.app')
@section('title', 'Catat Kematian Ikan')
@section('content')
<form action="{{ route('kematian.store') }}" method="POST" class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Pilih Kolam <span class="text-red-500">*</span></label>
        <select name="kolam_id" class="w-full border rounded p-2" required>
            <option value="">-- Pilih Kolam --</option>
            @foreach($kolams as $k)
                <option value="{{ $k->id }}">Kolam {{ $k->nomor_kolam }} (Populasi: {{ $k->jumlah }})</option>
            @endforeach
        </select>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Jumlah Mati (Ekor) <span class="text-red-500">*</span></label>
        <input type="number" name="jumlah_ekor" class="w-full border rounded p-2" min="1" required>
        @error('jumlah_ekor') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Tanggal Kejadian <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border rounded p-2" required>
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 mb-2">Catatan / Penyebab</label>
        <textarea name="catatan" class="w-full border rounded p-2" placeholder="Misal: Jamur, Kualitas Air, dll"></textarea>
    </div>
    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Simpan Data</button>
</form>
@endsection
